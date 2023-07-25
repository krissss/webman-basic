<?php

namespace app\admin\controller\repository;

use app\components\Tools;
use Illuminate\Contracts\Filesystem\Filesystem;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class FilesystemRepository extends AbsRepository
{
    protected Filesystem $disk;

    public function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
    }

    /**
     * {@inheritDoc}
     */
    protected function doCreate(array $data): void
    {
        throw new \InvalidArgumentException('Not support');
    }

    /**
     * {@inheritDoc}
     */
    protected function doUpdate(array $data, $id): void
    {
        throw new \InvalidArgumentException('Not support');
    }

    /**
     * {@inheritDoc}
     */
    protected function attributeLabels(): array
    {
        return [
            'id' => '序号',
            'path' => '路径',
            'is_dir' => '是否是目录',
            'dirname' => '目录',
            'file' => '文件名',
            'ext' => '扩展名',
            'time' => '修改时间',
            'size' => '大小',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array
    {
        $path = $search['dirname'] ?? null;
        $dirs = collect($this->disk->directories($path))
            ->map(fn (string $path) => ['path' => $path, 'dir' => true]);
        $files = collect($this->disk->files($path))
            ->map(fn (string $path) => ['path' => $path, 'dir' => false]);
        $all = $dirs->merge($files)
            ->filter(fn (array $item) => 0 !== strpos($item['path'], '.') && 0 !== strpos(basename($item['path']), '.'))
            ->values();
        $items = $all
            ->forPage($page, $perPage)
            ->map(function (array $item, int $index) {
                $pathInfo = $item['dir'] ? [
                    'dirname' => $item['path'],
                    'basename' => '',
                    'extension' => '',
                ] : pathinfo($item['path']);

                return [
                    'id' => $index + 1,
                    'path' => '/'.$item['path'],
                    'is_dir' => $item['dir'],
                    'dirname' => '/'.('.' === $pathInfo['dirname'] ? '' : $pathInfo['dirname']),
                    'file' => $pathInfo['basename'],
                    'ext' => $pathInfo['extension'] ?? '',
                    'time' => $this->disk->lastModified($item['path']),
                    'size' => $item['dir'] ? '' : Tools::formatBytes($this->disk->size($item['path'])),
                ];
            })
            ->values()
            ->toArray();

        return [
            'items' => $items,
            'total' => $all->count(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function detail($id): array
    {
        throw new \InvalidArgumentException('Not support');
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($id): void
    {
        throw new \InvalidArgumentException('Not support');
    }

    /**
     * {@inheritDoc}
     */
    public function recovery($id): void
    {
        throw new \InvalidArgumentException('Not support');
    }
}
