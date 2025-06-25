<?php

namespace app\admin\controller\repository;

use app\components\Tools;
use Illuminate\Contracts\Filesystem\Filesystem;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Helper\DTO\PresetItem;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;
use WebmanTech\AmisAdmin\Repository\AbsRepository;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;
use WebmanTech\AmisAdmin\Repository\HasPresetTrait;

class FilesystemRepository extends AbsRepository implements HasPresetInterface
{
    use HasPresetTrait;

    protected Filesystem $disk;

    protected function createPresetsHelper(): PresetsHelper
    {
        return (new PresetsHelper())
            ->withPresets([
                'id' => new PresetItem(
                    label: '序号',
                ),
                'path' => new PresetItem(
                    label: '路径',
                    filter: false,
                    grid: false,
                ),
                'is_dir' => new PresetItem(
                    label: '是否是目录',
                    filter: false,
                    grid: false,
                ),
                'dirname' => new PresetItem(
                    label: '目录',
                ),
                'file' => new PresetItem(
                    label: '文件名',
                    filter: false,
                ),
                'ext' => new PresetItem(
                    label: '扩展名',
                    filter: false,
                ),
                'time' => new PresetItem(
                    label: '修改时间',
                    filter: false,
                    gridExt: fn(GridColumn $column) => $column->typeDatetime(),
                ),
                'size' => new PresetItem(
                    label: '大小',
                    filter: false,
                ),
            ])
            ->withDefaultSceneKeys(['id', 'path', 'is_dir', 'dirname', 'file', 'ext', 'time', 'size']);
    }

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
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array
    {
        $path = $search['dirname'] ?? null;
        $dirs = collect($this->disk->directories($path))
            ->map(fn(string $path) => ['path' => $path, 'dir' => true]);
        $files = collect($this->disk->files($path))
            ->map(fn(string $path) => ['path' => $path, 'dir' => false]);
        $all = $dirs->merge($files)
            ->filter(fn(array $item) => strpos($item['path'], '.') !== 0 && strpos(basename($item['path']), '.') !== 0)
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
                    'path' => '/' . $item['path'],
                    'is_dir' => $item['dir'],
                    'dirname' => '/' . ($pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname']),
                    'file' => $pathInfo['basename'],
                    'ext' => $pathInfo['extension'] ?? '',
                    'time' => $item['dir'] ? '' : $this->disk->lastModified($item['path']), // 部分系统（比如oss）无法获取到文件夹的 meta 信息，所以不获取
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
