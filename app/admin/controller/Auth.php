<?php

namespace app\admin\controller;

use app\components\Component;
use app\model\Admin;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class Auth
{
    // 登录
    public function login(Request $request)
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new BadRequestHttpException('用户名或密码错误');
        }

        return json($admin);
    }
}
