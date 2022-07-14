<?php

namespace app\admin\controller;

use app\admin\controller\repository\AdminRepository;
use app\components\Component;
use app\exception\ValidationException;
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
                'type' => 'tabs',
                'tabsMode' => 'strong',
                'tabs' => [
                    [
                        'title' => '基本信息',
                        'tab' => [
                            'type' => 'form',
                            'title' => '基本信息',
                            'mode' => 'horizontal',
                            'initApi' => route('admin.info'),
                            'api' => 'post:' . route('admin.info.update'),
                            'body' => [
                                DetailAttribute::make()->name('username')->label('用户名'),
                                FormField::make()->name('name')->label('名称')->required(),
                            ],
                        ],
                    ],
                    [
                        'title' => '修改密码',
                        'tab' => [
                            'type' => 'form',
                            'title' => '修改密码',
                            'mode' => 'horizontal',
                            'api' => 'post:' . route('admin.info.change-password'),
                            'body' => [
                                ['type' => 'alert', 'body' => '修改密码后会刷新 Access Token', 'level' => 'info', 'showIcon' => true],
                                FormField::make()->name('old_password')->label('原密码')->required()->typeInputPassword(),
                                FormField::make()->name('new_password')->label('新密码')->required()->typeInputPassword(),
                                FormField::make()->name('new_password_confirmation')->label('新密码确认')->required()->typeInputPassword(),
                            ],
                        ],
                    ]
                ]
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
     */
    public function update(Request $request): Response
    {
        $this->repository()->update($request->only(['name']), Auth::guard()->getId());
        return admin_response('ok');
    }

    /**
     * 修改面目
     * @param Request $request
     * @return Response
     */
    public function changePassword(Request $request): Response
    {
        $validator = validator($request->only(['old_password']), [
            'old_password' => 'required|string',
        ]);
        $data = $validator->validate();

        $admin = Auth::identityAdmin();
        if (!Component::security()->validatePassword($data['old_password'], $admin->password)) {
            throw new ValidationException(['old_password' => '原密码错误']);
        }

        $this->repository()->resetPassword($request->only(['new_password', 'new_password_confirmation']), $admin->id);

        return (new AuthController())->logout();
    }

    protected function repository(): AdminRepository
    {
        return new AdminRepository();
    }
}
