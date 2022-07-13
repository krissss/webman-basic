<?php

namespace app\admin\controller;

use app\admin\controller\repository\AdminRepository;
use app\enums\AdminStatus;
use app\model\Admin;
use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Amis\DetailAttribute;
use Kriss\WebmanAmisAdmin\Amis\FormField;
use Kriss\WebmanAmisAdmin\Amis\GridColumn;
use Kriss\WebmanAmisAdmin\Repository\RepositoryInterface;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;

class AdminController extends AbsSourceController
{
    /**
     * @inheritdoc
     * @return AdminRepository
     */
    protected function repository(): RepositoryInterface
    {
        return new AdminRepository();
    }

    /**
     * @inheritdoc
     */
    protected function authDestroy($id = null): bool
    {
        if ($id == Admin::SUPER_ADMIN_ID) {
            return false;
        }
        if ($id == Auth::guard()->getId()) {
            return false;
        }
        return parent::authDestroy($id);
    }

    /**
     * @inheritdoc
     */
    protected function authDestroyVisible(): string
    {
        return implode(' && ', [
            parent::authDestroyVisible(),
            'this.id !=' . Admin::SUPER_ADMIN_ID,
            'this.id !=' . Auth::guard()->getId(), // 不能删除自己
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function grid(): array
    {
        return [
            'id',
            GridColumn::make()->name('username')->searchable(),
            GridColumn::make()->name('name')->searchable()->quickEdit(),
            GridColumn::make()->name('status')->sortable()->searchable()
                ->typeMapping(['map' => AdminStatus::getViewItems()]),
            GridColumn::make()->name('created_at')->sortable()->searchable([
                'type' => 'input-datetime-range',
            ]),
            GridColumn::make()->name('updated_at')->toggled(false),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function gridActions(string $routePrefix): Amis\GridColumnActions
    {
        return parent::gridActions($routePrefix)
            ->withButtonDialog(
                Amis\GridColumnActions::INDEX_UPDATE + 1,
                '重置密码',
                $this->buildFormFields([
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
                    'api' => route('admin.reset-password', ['id' => '${id}']),
                    'level' => 'warning',
                ]
            );
    }

    /**
     * @inheritdoc
     */
    protected function form(string $scene): array
    {
        $isRequired = $scene === static::SCENE_CREATE;
        $form = [
            FormField::make()->name('username')->required($isRequired),
            FormField::make()->name('name')->required($isRequired),
        ];
        if ($scene === static::SCENE_CREATE) {
            $form[] = FormField::make()->name('password')->typeInputPassword()->required($isRequired);
        }
        if ($scene === static::SCENE_UPDATE) {
            $form[] = FormField::make()->name('status')->typeSelect([
                'options' => AdminStatus::getLabelValue()
            ]);
        }
        return $form;
    }

    /**
     * @inheritdoc
     */
    protected function detail(): array
    {
        return [
            'username',
            'name',
            DetailAttribute::make()->name('status')->typeMapping(['map' => AdminStatus::getViewItems()]),
            'access_token',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * 重置密码
     * @param Request $request
     * @return Response
     */
    public function resetPassword(Request $request): Response
    {
        $this->repository()->resetPassword($request->post());
        return admin_response(['result' => 'ok']);
    }
}
