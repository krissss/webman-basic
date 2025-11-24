<?php

namespace app\admin\controller;

use app\admin\controller\repository\UserRepository;
use app\enums\UserStatusEnum;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

/**
 * @method UserRepository repository()
 */
class UserController extends AbsSourceController
{
    /**
     * {@inheritdoc}
     */
    protected function createRepository(): RepositoryInterface
    {
        return new UserRepository;
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
                    'schemaApi' => 'get:' . route('admin.user.resetPassword.get', ['id' => '$id']),
                ],
                [
                    'api' => [
                        'url' => route("admin.user.resetPassword.post", ['id' => '$id']),
                    ],
                    'level' => 'warning',
                ],
            )
            ->withButtonAjax(
                Amis\GridColumnActions::INDEX_DETAIL - 1,
                '登录',
                route('admin.user.login', ['id' => '${id}']),
                [
                    'level' => 'success',
                    'visibleOn' => 'this.status==' . UserStatusEnum::Enabled->value,
                ]
            )
            ->withButtonAjax(
                Amis\GridColumnActions::INDEX_UPDATE + 2,
                '重置Token',
                route('admin.user.resetApiToken', ['id' => '${id}']),
                [
                    'level' => 'danger',
                    'confirmText' => '重置 api_token 将导致外部接口无法调用，确认？',
                ]
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
                    ...$this->repository()->getPresetsHelper()->withScene(UserRepository::SCENE_RESET_PASSWORD)->pickForm(),
                ]),
            ]);
        }

        $this->repository()->resetPassword($request->post(), $id);

        return admin_response('ok');
    }

    /**
     * 重置 ApiToken.
     */
    public function resetApiToken(Request $request, $id): Response
    {
        $this->repository()->resetApiToken($id);

        return admin_response('ok');
    }

    /**
     * 登录.
     */
    public function login(Request $request, $id): Response
    {
        return admin_redirect(route('user.login.admin', [
            'accessToken' => Auth::identityAdmin()->access_token,
            'id' => $id,
        ]), '', '_blank');
    }
}
