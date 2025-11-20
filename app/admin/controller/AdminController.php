<?php

namespace app\admin\controller;

use app\admin\controller\repository\AdminRepository;
use app\components\Component;
use app\model\Admin;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
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
        return new AdminRepository;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function authDestroyVisible(): string
    {
        return implode(' && ', [
            parent::authDestroyVisible(),
            'this.id != "' . Admin::SUPER_ADMIN_ID . '"',
            'this.id != "' . Auth::guard()->getId() . '"', // 不能删除自己
        ]);
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
                [
                    'type' => 'service',
                    'schemaApi' => 'get:' . route('admin.admin.resetPassword.get', ['id' => '$id']),
                ],
                [
                    'api' => [
                        'url' => route("admin.admin.resetPassword.post", ['id' => '$id']),
                    ],
                    'level' => 'warning',
                ],
            );
    }

    /**
     * 重置密码
     */
    public function resetPassword(Request $request, $id): Response
    {
        if ($request->method() === 'GET') {
            return admin_response([
                'type' => 'container',
                'body' => $this->buildFormFields([
                    ['type' => 'alert', 'body' => '重置密码后会刷新 Access Token', 'level' => 'info', 'showIcon' => true],
                    ...$this->repository()->getPresetsHelper()->withScene(AdminRepository::SCENE_RESET_PASSWORD)->pickForm(),
                ]),
            ]);
        }

        $data = $this->repository()->validate($request->post(), AdminRepository::SCENE_RESET_PASSWORD);
        $model = Admin::query()->findOrFail($id);
        $model->password = Component::security()->generatePasswordHash($data['new_password']);
        $model->refreshToken();

        return admin_response('ok');
    }
}
