<?php

namespace app\admin\controller;

use app\admin\controller\repository\FilesystemRepository;
use app\components\fileUpload\amis\AmisFileUpload;
use app\components\fileUpload\amis\InputFile;
use Illuminate\Contracts\Filesystem\Cloud;
use support\facade\Storage;
use Webman\Http\Request;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

class FilesystemController extends AbsSourceController
{
    protected bool $onlyShow = true;
    protected ?string $diskName = null;

    /**
     * 是否展示上传的例子
     * 为 null 时 debug 模式下启用，否则关闭.
     */
    protected ?bool $enableUploadExample = null;

    public function __construct()
    {
        if (!\is_bool($this->enableUploadExample)) {
            $this->enableUploadExample = config('app.debug');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createRepository(): RepositoryInterface
    {
        return new FilesystemRepository($this->disk());
    }

    protected function disk(): Cloud
    {
        $disk = Storage::disk($this->diskName);
        if ($disk instanceof Cloud) {
            return $disk;
        }
        throw new \InvalidArgumentException('disk must be instance of Illuminate\Contracts\Filesystem\Cloud');
    }

    /**
     * {@inheritDoc}
     */
    protected function grid(): array
    {
        return [
            Amis\GridColumn::make()->name('id'),
            Amis\GridColumn::make()->name('dirname')->searchable(),
            Amis\GridColumn::make()->name('file'),
            Amis\GridColumn::make()->name('ext'),
            Amis\GridColumn::make()->name('size'),
            Amis\GridColumn::make()->name('time')->typeDatetime(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function amisCrud(Request $request): Amis\Crud
    {
        $crud = parent::amisCrud($request)
            ->schema([
                'syncLocation' => true,
            ])
            ->withHeaderToolbar(Amis\Crud::INDEX_COLUMNS_TOGGLE + 1, [
                'type' => 'button',
                'label' => '返回上级',
                'align' => 'right',
                'onClick' => implode('', [
                    'var href=window.location.href;',
                    'var m=href.match(/(\?|&)dirname=([^&]*)(&|$)/);',
                    'if(!m||!m[2]){return;}',
                    'var dir=m[2].replace(/%2F/,\'/\');',
                    'var dir=dir.substr(0, dir.lastIndexOf(\'/\'));',
                    'window.location.href=href.replace(/\?(.*)/,\'?dirname=\'+dir)',
                ]),
                'level' => 'success',
                'visibleOn' => '/(\?|&)dirname=([^&]{1,})(&|$)/.test(window.location.href)',
            ]);
        if ($this->enableUploadExample) {
            $crud->withHeaderToolbar(
                Amis\Crud::INDEX_CREATE + 1,
                Amis\ActionButtons::make()
                    ->withButtonDialog(
                        1,
                        '上传图片-立即',
                        $this->buildFormFields([
                            Amis\FormField::make()->name('dir')->value('/images')->disabled(),
                            Amis\FormField::make()->name('file')->typeInputFile(
                                InputFile::make()
                                    ->withAcceptExtensions('jpg,jpeg,png')
                                    ->withUseChunk(false)
                                    ->withUploadApi('admin.filesystem.uploadImage')
                                    ->toArray()
                            ),
                        ]),
                        [
                            'level' => 'primary',
                        ]
                    )
                    ->withButtonDialog(
                        2,
                        '上传图片-表单',
                        $this->buildFormFields([
                            Amis\FormField::make()->name('dir')->value('/'),
                            Amis\FormField::make()->name('file')->typeInputFile(
                                InputFile::make()
                                    ->withAcceptMimes('image/*')
                                    ->withForm()
                                    ->toArray()
                            ),
                        ]),
                        [
                            'level' => 'primary',
                            'api' => 'post:'.route(
                                'admin.filesystem.uploadImage',
                                ['type' => AmisFileUpload::TYPE_SINGLE]
                            ),
                        ]
                    )
                    ->withButtonDialog(
                        3,
                        '上传文件-自动分块',
                        $this->buildFormFields([
                            Amis\FormField::make()->name('dir')->value('/files')->disabled(),
                            Amis\FormField::make()->name('file')->typeInputFile(
                                InputFile::make()
                                    ->withAcceptExtensions('pdf')
                                    ->withUseChunk(null, 1 * 1024 * 1024)
                                    ->withUploadApi('admin.filesystem.uploadFile')
                                    ->toArray()
                            ),
                        ]),
                        [
                            'level' => 'primary',
                        ]
                    )
            );
        }

        return $crud;
    }

    /**
     * {@inheritDoc}
     */
    protected function authDetail($id = null): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        return parent::gridActions($routePrefix)
            ->withButtonLink(Amis\GridColumnActions::INDEX_DETAIL + 1, '进入', '?dirname=${dirname}', [
                'visibleOn' => '${is_dir}',
                'level' => 'success',
            ])
            ->withButtonAjax(
                Amis\GridColumnActions::INDEX_DETAIL + 2,
                '打开',
                'post:'.route('admin.filesystem.url'),
                [
                    'visibleOn' => '${!is_dir}',
                    'level' => 'info',
                    'redirect' => '${url|raw}',
                ]
            );
    }

    /**
     * 上传图片.
     *
     * @return \Webman\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadImage(Request $request, string $type = null)
    {
        if (!$this->enableUploadExample) {
            return amis_response(['forbidden']);
        }

        $fileUpload = new AmisFileUpload($request, $this->disk(), [
            'fileAttribute' => 'file',
            'rules' => 'required|file|image',
            'dir' => $request->post('dir', 'images'),
        ]);

        return amis_response($fileUpload->handle($type));
    }

    /**
     * 上传文件.
     *
     * @return \Webman\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadFile(Request $request, string $type = null)
    {
        if (!$this->enableUploadExample) {
            return amis_response(['forbidden']);
        }

        $fileUpload = new AmisFileUpload($request, $this->disk(), [
            'fileAttribute' => 'file',
            'rules' => 'required|file|mimes:pdf',
            'dir' => $request->post('dir', 'files'),
        ]);

        return amis_response($fileUpload->handle($type));
    }

    /**
     * 获取 url.
     *
     * @return \Webman\Http\Response
     */
    public function url(Request $request)
    {
        $path = $request->post('path');

        return amis_response([
            'url' => $this->disk()->url($path),
        ]);
    }
}
