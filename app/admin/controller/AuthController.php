<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\UserSeeException;
use app\model\Admin;
use support\facade\Auth;
use support\Request;
use support\Response;

class AuthController
{
    // 登录
    public function login(Request $request): Response
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new UserSeeException(trans('auth.user_password_error'));
        }

        Auth::guard()->login($admin);
        $admin->refreshToken();

        return json_success($admin);
    }

    // 退出登录
    public function logout(): Response
    {
        if (Auth::guard()->isGuest()) {
            return json_success('guest');
        }

        Auth::identityAdmin()->refreshToken(null);
        Auth::guard()->logout();

        return json_success('logout');
    }
}
