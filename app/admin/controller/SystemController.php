<?php

namespace app\admin\controller;

use support\facade\Auth;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis\Page;

/**
 * 系统信息.
 */
class SystemController
{
    private static ?array $menus = null;

    /**
     * admin 菜单.
     */
    public function pages(): Response
    {
        if (null === self::$menus) {
            self::$menus = [
                [
                    'label' => '菜单',
                    'children' => [
                        ['label' => '首页', 'icon' => 'fa fa-home', 'url' => '/', 'schemaApi' => route('admin.dashboard.view')],
                        ['label' => '个人设置', 'url' => '/admin/info', 'schemaApi' => route('admin.info.view'), 'visible' => false],
                        ['label' => '管理员管理', 'icon' => 'fa fa-user', 'url' => '/admin', 'schemaApi' => route('admin.admin.index')],
                        ['label' => '用户管理', 'icon' => 'fa fa-user', 'url' => '/user', 'schemaApi' => route('admin.user.index')],
                    ],
                ],
                [
                    'label' => '系统',
                    'children' => [
                        ['label' => '文件管理', 'icon' => 'fa fa-circle-o', 'url' => '/filesystem', 'schemaApi' => route('admin.filesystem.index')],
                        [
                            'label' => '日志查看', 'icon' => 'fa fa-file-text-o', 'url' => '/log-reader',
                            'schemaApi' => route('admin.iframe.view').'?link='.urlencode(config('plugin.webman-tech.log-reader.log-reader.route.group')),
                            'visible' => config('plugin.webman-tech.log-reader.app.enable'),
                        ],
                    ],
                ],
            ];
        }

        return admin_response([
            'pages' => self::$menus,
        ]);
    }

    /**
     * 首页.
     */
    public function dashboard(): Response
    {
        $page = Page::make()
            ->withBody(1, [
                'Hello '.Auth::getName(),
            ]);

        return admin_response($page);
    }

    /**
     * iframe 内嵌.
     */
    public function iframe(Request $request): Response
    {
        $page = Page::make()
            ->withBody(1, [
                'type' => 'iframe',
                'src' => urldecode($request->get('link')),
            ]);

        return admin_response($page);
    }
}
