<?php

namespace app\admin\controller;

use app\admin\controller\repository\UserRepository;
use app\enums\common\OnOffStatusEnum;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\FormField;
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
                    'api' => route('admin.user.resetPassword', ['id' => '${id}']),
                    'level' => 'warning',
                ]
            )
            ->withButtonAjax(
                Amis\GridColumnActions::INDEX_DETAIL - 1,
                '登录',
                route('admin.user.login', ['id' => '${id}']),
                [
                    'level' => 'success',
                    'visibleOn' => 'this.status==' . OnOffStatusEnum::On->value,
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
