<?php

namespace app\user\controller;

use Webman\Http\Response;

/**
 * 系统信息.
 */
class SystemController extends \app\admin\controller\SystemController
{
    /**
     * admin 菜单.
     */
    public function pages(): Response
    {
        $menus = [
            [
                'label' => '菜单',
                'children' => [
                    ['label' => '首页', 'icon' => 'fa fa-home', 'url' => '/', 'schemaApi' => route('user.dashboard.view')],
                    ['label' => '个人设置', 'url' => '/user/info', 'schemaApi' => route('user.info.view'), 'visible' => false],
                ],
            ],
        ];

        return admin_response([
            'pages' => $menus,
        ]);
    }
}
