<?php

namespace app\components\fileUpload\amis;

use app\components\fileUpload\FileUpload;
use Illuminate\Validation\ValidationException;

/**
 * amis 文件上传
 * 支持自动切换单文件上传和分块上传.
 */
class AmisFileUpload extends FileUpload
{
    public const TYPE_SINGLE = 'single';

    /**
     * @throws ValidationException
     */
    public function handle(string $type = null): array
    {
        if ($type !== static::TYPE_SINGLE) {
            return (new ChunkFileUpload($this->request, $this->filesystem, $this->config))
                ->handle($type);
        }

        return (new SingleFileUpload($this->request, $this->filesystem, $this->config))
            ->uploadForAmis();
    }

    /**
     * {@inheritDoc}
     */
    public function upload(): string
    {
        throw new \InvalidArgumentException('Not support');
    }
}
