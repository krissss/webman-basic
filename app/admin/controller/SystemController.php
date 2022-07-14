<?php

namespace app\admin\controller;

use Kriss\WebmanAmisAdmin\Amis\Page;
use support\facade\Auth;
use Webman\Http\Response;

/**
 * 系统信息
 */
class SystemController
{
    /**
     * admin 菜单
     * @return Response
     */
    public function pages(): Response
    {
        return admin_response([
            'pages' => [
                [
                    'label' => '菜单',
                    'children' => [
                        ['label' => '首页', 'icon' => 'fa fa-home', 'url' => '/', 'schemaApi' => route('admin.dashboard.view'),],
                        ['label' => '个人设置', 'url' => '/admin/info', 'schemaApi' => route('admin.info.view'), 'visible' => false],
                        ['label' => '管理员管理', 'icon' => 'fa fa-user', 'url' => '/admin', 'schemaApi' => route('admin.index')],
                    ],
                ],
            ]
        ]);
    }

    /**
     * 首页
     * @return Response
     */
    public function dashboard(): Response
    {
        $page = Page::make()
            ->withBody(1, [
                'Hello ' . Auth::identityAdmin()->name,
            ]);
        return admin_response($page);
    }
}
