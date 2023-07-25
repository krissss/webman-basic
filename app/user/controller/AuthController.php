<?php

namespace app\user\controller;

use app\model\Admin;
use app\model\User;
use support\facade\Auth;
use Webman\Http\Request;

/**
 * 授权，此处同 admin 的形式，可以按需修改掉.
 */
class AuthController extends \app\admin\controller\AuthController
{
    /**
     * {@inheritdoc}
     */
    protected string $model = User::class;
    /**
     * {@inheritdoc}
     */
    protected string $routeRedirect = '/user/login';

    /**
     * 从 admin 登录.
     *
     * @return \Webman\Http\Response
     */
    public function loginByAdmin(Request $request, string $accessToken, string $id)
    {
        Admin::where('access_token', $accessToken)->firstOrFail();
        /** @var User $user */
        $user = User::findOrFail($id);

        Auth::guard()->login($user);

        return redirect(route('user.layout'));
    }
}
