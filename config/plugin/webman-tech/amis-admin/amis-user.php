<?php

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Controller\RenderController;
use support\facade\Auth;

$adminAmis = require __DIR__ . '/amis.php';
$adminAmis['assets']['js'][] = '/js/amis-admin-user.js';

return [
    /**
     * amis 资源
     */
    'assets' => $adminAmis['assets'],
    /**
     * @see Amis::renderApp()
     */
    'app' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
         */
        'amisJSON' => function () {
            return [
                'brandName' => '管理后台',
                'logo' => '/favicon.ico',
                'api' => route('user.pages'), // 修改成获取菜单的路由
                'header' => [
                    'type' => 'flex',
                    'justify' => 'flex-end',
                    'style' => [
                        'width' => '100%',
                    ],
                    'items' => [
                        [
                            'type' => 'dropdown-button',
                            'label' => Auth::guard()->isGuest() ? '' : Auth::getName(),
                            'trigger' => 'hover',
                            'icon' => 'fa fa-user-circle',
                            'buttons' => Amis\ActionButtons::make()
                                ->withButtonLink(1, '个人设置', '/user/info')
                                ->withDivider(80)
                                ->withButtonAjax(99, '退出登录', route('user.logout'), [
                                    'confirmText' => '确定退出登录？'
                                ])
                                ->toArray(),
                        ],
                    ],
                ],
            ];
        },
        'title' => config('app.name'),
    ],
    /**
     * @see Amis::renderPage()
     */
    'page' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started
         */
        'amisJSON' => [],
    ],
    /**
     * 登录页面配置
     * @see RenderController::login()
     */
    'page_login' => function() {
        $debug = config('app.debug');
        return [
            //'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'login_api' => route('user.login'),
            'success_redirect' => route('user.layout'),
            'form' => [
                Amis\FormField::make()->name('username')->label('用户名')->required()->value($debug ? 'test' : ''),
                Amis\FormField::make()->name('password')->label('密码')->typeInputPassword()->required()->value($debug ? '123456' : ''),
            ],
        ];
    },
    /**
     * 用于全局替换组件的默认参数
     * @see Component::$config
     */
    'components' => $adminAmis['components'],
    /**
     * 默认的验证器
     * 返回一个 \WebmanTech\AmisAdmin\Validator\ValidatorInterface
     */
    'validator' => $adminAmis['validator'],
];
