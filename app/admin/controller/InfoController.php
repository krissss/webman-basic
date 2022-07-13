<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin;
use Kriss\WebmanAmisAdmin\Amis\DetailAttribute;
use Kriss\WebmanAmisAdmin\Amis\FormField;
use Kriss\WebmanAmisAdmin\Amis\Page;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;

/**
 * 当前登录用户信息
 */
class InfoController
{
    /**
     * 用户信息页面
     * @return Response
     */
    public function page(): Response
    {
        $page = Page::make()
            ->withBody(1, [
                'type' => 'form',
                'title' => '用户信息',
                'mode' => 'horizontal',
                'initApi' => route('admin.info'),
                'api' => 'post:' . route('admin.info.update'),
                'body' => [
                    DetailAttribute::make()->name('username')->label('用户名'),
                    FormField::make()->name('name')->label('名称')->required(),
                    FormField::make()->name('password')->label('密码')->typeInputPassword(),
                ],
            ]);
        return admin_response($page);
    }

    /**
     * 登录用户信息
     * @return Response
     */
    public function index(): Response
    {
        return admin_response(Auth::guard()->getUser());
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request): Response
    {
        $validator = validator($request->post(), [
            'name' => 'required|string|max:32',
            'password' => 'string|min:6|max:32',
        ]);
        $data = $validator->validate();

        $admin = Auth::identityAdmin();
        $admin->name = $data['name'];
        if (isset($data['password']) && $data['password']) {
            $admin->password = $data['password'];
        }
        if ($admin->isDirty('password')) {
            $admin->password = Component::security()->generatePasswordHash($admin->password);
        }
        $admin->save();
        return admin_response('ok');
    }
}
