<?php

namespace app\admin\controller;

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
        $prefix = '/admin';
        return admin_response([
            'pages' => [
                [
                    'label' => '菜单',
                    'children' => [
                        ['label' => '管理员管理', 'icon' => 'fa fa-file', 'url' => '/admin', 'schemaApi' => $prefix . '/admin']
                    ],
                ],
            ]
        ]);
    }
}
