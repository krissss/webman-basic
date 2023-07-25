<?php

namespace app\admin\controller;

use app\admin\controller\repository\AdminRepository;
use app\enums\AdminStatus;
use app\model\Admin;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

/**
 * @method AdminRepository repository()
 */
class AdminController extends AbsSourceController
{
    /**
     * {@inheritdoc}
     */
    protected function createRepository(): RepositoryInterface
    {
        return new AdminRepository();
    }

    /**
     * {@inheritdoc}
     */
    protected function authDestroy($id = null): bool
    {
        if (Admin::SUPER_ADMIN_ID == $id) {
            return false;
        }
        if ($id == Auth::guard()->getId()) {
            return false;
        }

        return parent::authDestroy($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function authDestroyVisible(): string
    {
        return implode(' && ', [
            parent::authDestroyVisible(),
            'this.id != "'.Admin::SUPER_ADMIN_ID.'"',
            'this.id != "'.Auth::guard()->getId().'"', // 不能删除自己
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function grid(): array
    {
        return [
            GridColumn::make()->name('id')->sortable(),
            GridColumn::make()->name('username')->searchable(),
            GridColumn::make()->name('name')->searchable()->quickEdit(),
            GridColumn::make()->name('status')->searchable()->quickEdit()
                ->typeMapping(['map' => AdminStatus::getViewLabeledItems()]),
            GridColumn::make()->name('created_at')->sortable()->searchable([
                'type' => 'input-datetime-range',
            ]),
            GridColumn::make()->name('updated_at')->toggled(false),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function form(string $scene): array
    {
        $isRequired = $scene === static::SCENE_CREATE;
        $form = [
            FormField::make()->name('username')->required($isRequired),
            FormField::make()->name('name')->required($isRequired),
        ];
        if ($scene === static::SCENE_CREATE) {
            $form[] = FormField::make()->name('password')->required($isRequired)
                ->typeInputPassword();
        }
        if ($scene === static::SCENE_UPDATE) {
            $form[] = FormField::make()->name('status')
                ->typeSelect(['options' => AdminStatus::getLabelValue()]);
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function detail(): array
    {
        return [
            'id',
            'username',
            'name',
            DetailAttribute::make()->name('status')->typeMapping(['map' => AdminStatus::getViewLabeledItems()]),
            'access_token',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        return parent::gridActions($routePrefix)
            ->withButtonDialog(
                Amis\GridColumnActions::INDEX_UPDATE + 1,
                '重置密码',
                $this->buildFormFields([
                    ['type' => 'alert', 'body' => '重置密码后会刷新 Access Token', 'level' => 'info', 'showIcon' => true],
                    FormField::make()->typeInputPassword()->name('new_password')->value(null)->required(),
                    FormField::make()->typeInputPassword()->name('new_password_confirmation')->value(null)->required()->schema([
                        'validations' => [
                            'equalsField' => 'new_password',
                        ],
                        'validationErrors' => [
                            'equalsField' => '两次密码输入不一致',
                        ],
                    ]),
                ]),
                [
                    'api' => route('admin.admin.resetPassword', ['id' => '${id}']),
                    'level' => 'warning',
                ]
            );
    }

    /**
     * 重置密码
     */
    public function resetPassword(Request $request, $id): Response
    {
        $this->repository()->resetPassword($request->post(), $id);

        return admin_response('ok');
    }
}
