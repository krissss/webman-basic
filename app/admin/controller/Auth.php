<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin;
use Respect\Validation\Validator;
use support\Request;

class Auth
{
    // 登录
    public function login(Request $request)
    {
        $data = Validator::input($request->post(), [
            'username' => Validator::length(0, 32)->setName('用户名'),
            'password' => Validator::length(0, 32)->setName('密码'),
        ]);
        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new ValidationException('用户名或密码错误');
        }

        return json($admin);
    }
}
