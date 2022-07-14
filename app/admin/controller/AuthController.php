<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;

/**
 * 授权
 */
class AuthController
{
    /**
     * 登录
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new ValidationException([
                'username' => trans('用户名或密码错误'),
            ]);
        }

        Auth::guard()->login($admin);
        $admin->refreshToken();
        $admin->makeVisible(['access_token']);

        return admin_response($admin);
    }

    /**
     * 退出登录
     * @return Response
     */
    public function logout(): Response
    {
        if (!Auth::guard()->isGuest()) {
            Auth::identityAdmin()->refreshToken(null);
            Auth::guard()->logout();
        }

        return admin_redirect(route('admin.login.view'), '退出成功');
    }
}
