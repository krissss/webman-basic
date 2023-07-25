<?php

namespace app\components\fileUpload;

use app\exception\FileExistsException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Webman\Http\Request;
use WebmanTech\Polyfill\LaravelRequest;

/**
 * 文件上传.
 */
class FileUpload
{
    protected const FILENAME_KEEP = 'keep';

    protected Request $request;
    protected Filesystem $filesystem;
    protected array $config = [
        /*
         * string
         * 上传的文件 post 字段名
         */
        'fileAttribute' => 'file',
        /*
         * string|array
         * 验证规则，为空时不校验
         */
        'rules' => 'required|file',
        /*
         * string
         * 为空字符串或者 . 时表示根目录
         */
        'dir' => '',
        /*
         * null|string|callback
         * 当为 keep 时将保留原文件名，注意此时如果不校验文件是否存在时会覆盖原来的文件
         * 当为 callback 时，function(UploadedFile $file): string {} 返回一个自定义的文件名
         * 其他值为随机文件名
         */
        'filename' => null,
        /*
         * bool
         * 是否校验文件名是否已存在
         */
        'checkExist' => false,
    ];

    public function __construct(Request $request, Filesystem $filesystem, array $config = [])
    {
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->config = array_merge($this->config, $config);
    }

    protected ?UploadedFile $uploadedFile = null;

    /**
     * @throws ValidationException|FileExistsException
     */
    public function upload(): string
    {
        // 验证
        $laravelRequest = LaravelRequest::wrapper($this->request);
        $this->validateFile($laravelRequest);
        // 构造 path
        $this->uploadedFile = $file = $laravelRequest->file($this->config['fileAttribute']);
        $filename = $this->buildFilename($file);
        $path = $this->buildPath($filename);
        // 保存
        $this->storeFile($file, $path);

        return $path;
    }

    public function getUploadedFile(): UploadedFile
    {
        if (!$this->uploadedFile) {
            throw new \InvalidArgumentException('must call upload first');
        }

        return $this->uploadedFile;
    }

    /**
     * @throws ValidationException
     */
    protected function validateFile(LaravelRequest $request): void
    {
        if ($this->config['rules']) {
            validator($request->all(), [
                $this->config['fileAttribute'] => $this->config['rules'],
            ])->validate();
        }
    }

    protected function buildFilename(UploadedFile $file): string
    {
        if ($this->config['filename'] === static::FILENAME_KEEP) {
            $filename = $file->getClientOriginalName();
        } elseif ($this->config['filename'] instanceof \Closure) {
            $filename = \call_user_func($this->config['filename'], $file);
        } else {
            $filename = $file->hashName();
        }

        return $filename;
    }

    protected function buildPath(string $filename): string
    {
        $dir = '.' === $this->config['dir'] ? '' : $this->config['dir'];

        return ltrim($dir.'/', '/').$filename;
    }

    /**
     * @param UploadedFile|resource $file
     *
     * @throws FileExistsException
     */
    protected function storeFile($file, string $path): void
    {
        if ($this->config['checkExist'] && $this->filesystem->exists($path)) {
            throw new FileExistsException($path);
        }
        $this->filesystem->put($path, $file instanceof UploadedFile ? fopen($file->getRealPath(), 'r') : $file);
    }
}
