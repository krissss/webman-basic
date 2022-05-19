<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin;
use support\Request;

class Auth
{
    // 登录
    public function login(Request $request)
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first());
        }
        $data = $validator->validated();
        //$data = $validator->validate();

        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new ValidationException('用户名或密码错误');
        }

        return json($admin);
    }
}
