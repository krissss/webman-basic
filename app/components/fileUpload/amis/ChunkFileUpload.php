<?php

namespace app\components\fileUpload\amis;

use app\components\fileUpload\FileUpload;
use app\components\Tools;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use support\Cache;
use support\Log;
use WebmanTech\AmisAdmin\Exceptions\ValidationException as AmisValidationException;
use WebmanTech\Polyfill\LaravelRequest;
use WebmanTech\Polyfill\LaravelUploadedFile;

/**
 * 分块上传.
 *
 * @see https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-file#%E5%88%86%E5%9D%97%E4%B8%8A%E4%BC%A0
 */
class ChunkFileUpload extends FileUpload
{
    public const TYPE_START = 'start';
    public const TYPE_UPLOAD = 'upload';
    public const TYPE_FINISH = 'finish';

    /**
     * @throws ValidationException|AmisValidationException
     */
    public function handle(string $type): array
    {
        if ($type === static::TYPE_START) {
            return $this->handleStart();
        }
        if ($type === static::TYPE_UPLOAD) {
            return $this->handleUpload();
        }
        if ($type === static::TYPE_FINISH) {
            return $this->handleFinish();
        }
        throw new \InvalidArgumentException('type error');
    }

    /**
     * @throws ValidationException
     */
    protected function handleStart(): array
    {
        $data = validator($this->request->post(), [
            'filename' => 'required|string',
        ])->validated();
        $filename = Str::random(40);
        if ($ext = pathinfo($data['filename'], \PATHINFO_EXTENSION)) {
            $filename .= '.'.$ext;
        }
        $path = $this->buildPath($filename);

        return [
            'uploadId' => $this->buildUploadId($path),
            'key' => $path,
        ];
    }

    /**
     * @throws ValidationException
     */
    protected function handleUpload(): array
    {
        $laravelRequest = LaravelRequest::wrapper($this->request);
        $data = validator($laravelRequest->all(), [
            'uploadId' => 'required|string',
            'key' => 'required|string',
            'partNumber' => 'required|integer',
            'partSize' => 'required|integer',
            'file' => 'required|file',
        ])->validated();
        $this->checkUploadId($data['key'], $data['uploadId']);
        if (1 == $data['partNumber']) {
            // 第一个分块校验文件类型
            try {
                $this->validateFile($laravelRequest);
            } catch (ValidationException $e) {
                $this->stopNextUpload($data['uploadId']);
                throw $e;
            }
        }
        /** @var LaravelUploadedFile $file */
        $file = $data['file'];
        if ($file->getSize() !== (int) $data['partSize']) {
            throw Tools::buildValidationException(['partSize' => 'partSize error']);
        }
        $path = $this->buildChunkPath($data['uploadId'], $data['partNumber']);
        $this->storeFile($file, $path);

        return [
            'eTag' => md5_file($file->getRealPath()),
        ];
    }

    /**
     * @throws ValidationException
     */
    protected function handleFinish(): array
    {
        $data = validator($this->request->post(), [
            'filename' => 'required|string',
            'uploadId' => 'required|string',
            'key' => 'nullable|string',
            'partList' => 'required|array',
            'partList.*.partNumber' => 'required|integer',
            'partList.*.eTag' => 'required|string',
        ])->validated();
        try {
            $this->checkUploadId($data['key'], $data['uploadId']);
        } catch (\Throwable $e) {
            $this->cleanStopKey($data['uploadId']);
            throw $e;
        }
        /**
         * 合并分块.
         *
         * @see https://github.com/thephpleague/flysystem/issues/1288
         */
        $combinedFile = tmpfile();
        foreach ($data['partList'] as $part) {
            $path = $this->buildChunkPath($data['uploadId'], $part['partNumber']);
            $handle = $this->filesystem->readStream($path);
            stream_copy_to_stream($handle, $combinedFile);
        }
        // 保存新的文件
        $path = $data['key'];
        if ($this->config['filename'] === static::FILENAME_KEEP) {
            $path = $this->buildPath($data['filename']);
        }
        try {
            $this->storeFile($combinedFile, $path);
        } finally {
            // 清理相关资源
            $dir = $this->buildChunkPath($data['uploadId']);
            try {
                $this->filesystem->deleteDirectory($dir);
            } catch (\Throwable $e) {
                // 忽略删除错误
                Log::warning($e);
            }
        }

        return [
            'value' => $path,
        ];
    }

    protected function buildChunkPath(string $uploadId, string $partNumber = null): string
    {
        if ($partNumber) {
            $partNumber .= '.chunk';
        }

        return rtrim(".chunk/{$uploadId}/{$partNumber}", '/');
    }

    protected function buildUploadId(string $key): string
    {
        return md5(($this->config['uploadIdSalt'] ?? '').$key);
    }

    protected function checkUploadId(string $key, string $uploadId): void
    {
        if ($this->shouldStopUpload($uploadId)) {
            throw Tools::buildValidationException(['uploadId' => 'stop upload']);
        }
        if ($this->buildUploadId($key) !== $uploadId) {
            $this->stopNextUpload($uploadId);
            throw Tools::buildValidationException(['uploadId' => 'uploadId error']);
        }
    }

    protected function stopNextUpload(string $uploadId)
    {
        Cache::set($this->buildStopNextKey($uploadId), 1, 300);
    }

    protected function shouldStopUpload(string $uploadId): bool
    {
        return (bool) Cache::get($this->buildStopNextKey($uploadId));
    }

    protected function cleanStopKey(string $uploadId): void
    {
        Cache::delete($this->buildStopNextKey($uploadId));
    }

    protected function buildStopNextKey(string $uploadId): string
    {
        return 'stop_chunkUpload_'.$uploadId;
    }
}
