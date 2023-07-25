<?php

namespace app\components\fileUpload\amis;

use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\Polyfill\LaravelUploadedFile;

class InputFile extends Component
{
    protected array $schema = [
        'asBlob' => false,
        'maxSize' => 'auto',
    ];

    /**
     * @param string|array $mimes 举例：images/*,text/plain
     *
     * @return $this
     */
    public function withAcceptMimes($mimes): self
    {
        $this->schema['accept'] = \is_array($mimes) ? implode(',', $mimes) : $mimes;

        return $this;
    }

    /**
     * @param string|array $extensions 举例：jpg/jpeg/png
     *
     * @return $this
     */
    public function withAcceptExtensions($extensions): self
    {
        $this->schema['accept'] = array_map(
            fn (string $ext) => '.'.ltrim($ext, '.'),
            \is_string($extensions) ? explode(',', $extensions) : $extensions
        );

        return $this;
    }

    /**
     * @param bool|null $enable 为 null 时自动
     *
     * @return $this
     */
    public function withUseChunk(?bool $enable = null, ?int $chunkSize = null): self
    {
        $this->schema['useChunk'] = $enable ?? 'auto';
        if ($chunkSize) {
            $this->schema['chunkSize'] = $chunkSize;
        }

        return $this;
    }

    public function withUploadApi(string $routeName): self
    {
        $this->schema['asBlob'] = false;
        $this->schema['asBase64'] = false;
        $this->schema['receiver'] = 'post:'.route($routeName, ['type' => AmisFileUpload::TYPE_SINGLE]);
        $this->schema['startChunkApi'] = 'post:'.route($routeName, ['type' => ChunkFileUpload::TYPE_START]);
        $this->schema['chunkApi'] = 'post:'.route($routeName, ['type' => ChunkFileUpload::TYPE_UPLOAD]);
        $this->schema['finishChunkApi'] = 'post:'.route($routeName, ['type' => ChunkFileUpload::TYPE_FINISH]);

        return $this;
    }

    public function withForm(): self
    {
        $this->schema['asBlob'] = true;
        unset(
            $this->schema['receiver'],
            $this->schema['startChunkApi'],
            $this->schema['chunkApi'],
            $this->schema['finishChunkApi']
        );

        return $this;
    }

    public function toArray(): array
    {
        if ('auto' === $this->schema['maxSize']) {
            $this->schema['maxSize'] = LaravelUploadedFile::getMaxFilesize();
        }

        return parent::toArray();
    }
}
