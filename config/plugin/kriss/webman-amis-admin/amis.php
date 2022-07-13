<?php

use Kriss\WebmanAmisAdmin\Amis;
use Kriss\WebmanAmisAdmin\Amis\Component;
use Kriss\WebmanAmisAdmin\Controller\RenderController;
use Kriss\WebmanAmisAdmin\Validator\LaravelValidator;
use support\facade\Auth;

return [
    /**
     * amis 资源
     */
    'assets' => [
        /**
         * html 上的 lang 属性
         */
        'lang' => config('translation.locale', 'zh'),
        /**
         * 静态资源，建议下载下来放到 public 目录下然后替换链接
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#sdk
         */
        'css' => [
            'https://unpkg.com/amis/sdk/sdk.css',
            'https://unpkg.com/amis/sdk/helper.css',
            'https://unpkg.com/amis/sdk/iconfont.css',
        ],
        'js' => [
            'https://unpkg.com/amis/sdk/sdk.js',
            'https://unpkg.com/history@4.10.1/umd/history.js', // 使用 app 必须
            '/js/amis-admin.js',
        ],
        /**
         * 切换主题
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#%E5%88%87%E6%8D%A2%E4%B8%BB%E9%A2%98
         */
        'theme' => '',
        /**
         * 语言
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/i18n
         */
        'locale' => str_replace('_', '-', config('translation.locale', 'zh-CN')),
        /**
         * debug
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/debug
         */
        'debug' => false,
    ],
    /**
     * @see Amis::renderApp()
     */
    'app' => [
        /**
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
         */
        'amisJSON' => function () {
            return [
                'brandName' => config('app.name', 'App Admin'),
                'logo' => '/favicon.ico',
                'api' => route('admin.pages'), // 修改成获取菜单的路由
                'header' => [
                    'type' => 'flex',
                    'justify' => 'flex-end',
                    'style' => [
                        'width' => '100%',
                    ],
                    'items' => [
                        [
                            'type' => 'dropdown-button',
                            'label' => Auth::guard()->isGuest() ? '' : Auth::identityAdmin()->name,
                            'trigger' => 'hover',
                            'icon' => 'fa fa-user-circle',
                            'buttons' => Amis\ActionButtons::make()
                                ->withButtonLink(1, '修改信息', '/admin/info')
                                ->withButtonAjax(99, '退出登录', route('admin.logout'), [
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
    'page_login' => [
        //'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
        'login_api' => route('admin.login'),
        'success_redirect' => route('admin.layout'),
    ],
    /**
     * 用于全局替换组件的默认参数
     * @see Component::$config
     */
    'components' => [
        // 例如: 将列表页的字段默认左显示
        /*\Kriss\WebmanAmisAdmin\Amis\GridColumn::class => [
            'schema' => [
                'align' => 'left',
            ],
        ],*/
    ],
    /**
     * 默认的验证器
     * 返回一个 \Kriss\WebmanAmisAdmin\Validator\ValidatorInterface
     */
    'validator' => fn() => new LaravelValidator(validator()->getFactory()),
];
