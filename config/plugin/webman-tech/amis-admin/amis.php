<?php

use support\facade\Auth;
use support\facade\Validator;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Controller\RenderController;
use WebmanTech\AmisAdmin\Validator\LaravelValidator;

/**
 * Amis 静态资源的基础 url
 * 建议生产环境使用指定的版本，否则会存在因版本变更引起的问题
 * 更加建议使用公司自己的 cdn，或者将静态资源下载后放到本地，提交速度.
 */
// $amisAssetBaseUrl = 'https://unpkg.com/amis@2.2.0/sdk/';
$amisAssetBaseUrl = '/assets/amis@2.2.0/sdk/';

return [
    /*
     * amis 资源
     */
    'assets' => [
        /*
         * html 上的 lang 属性
         */
        'lang' => config('translation.locale', 'zh'),
        /*
         * 静态资源，建议下载下来放到 public 目录下然后替换链接
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#sdk
         */
        'css' => [
            $amisAssetBaseUrl.'sdk.css',
            $amisAssetBaseUrl.'helper.css',
            $amisAssetBaseUrl.'iconfont.css',
        ],
        'js' => [
            $amisAssetBaseUrl.'sdk.js',
            // 'https://unpkg.com/history@4.10.1/umd/history.js', // 使用 app 必须
            '/assets/history@4.10.1/umd/history.min.js', // 使用 app 必须
            '/js/amis-admin.js',
        ],
        /*
         * 切换主题
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started#%E5%88%87%E6%8D%A2%E4%B8%BB%E9%A2%98
         */
        'theme' => '',
        /*
         * 语言
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/i18n
         */
        'locale' => str_replace('_', '-', config('translation.locale', 'zh-CN')),
        /*
         * debug
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/extend/debug
         */
        'debug' => false,
    ],
    /*
     * @see Amis::renderApp()
     */
    'app' => [
        /*
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/app
         */
        'amisJSON' => function () {
            return [
                'brandName' => '总管理后台',
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
                            'label' => Auth::guard()->isGuest() ? '' : Auth::getName(),
                            'trigger' => 'hover',
                            'icon' => 'fa fa-user-circle',
                            'buttons' => Amis\ActionButtons::make()
                                ->withButtonLink(1, '个人设置', '/admin/info')
                                ->withDivider(80)
                                ->withButtonAjax(99, '退出登录', route('admin.logout'), [
                                    'confirmText' => '确定退出登录？',
                                ])
                                ->toArray(),
                        ],
                    ],
                ],
            ];
        },
        'title' => config('app.name'),
    ],
    /*
     * @see Amis::renderPage()
     */
    'page' => [
        /*
         * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/start/getting-started
         */
        'amisJSON' => [],
    ],
    /*
     * 登录页面配置
     * @see RenderController::login()
     */
    'page_login' => function () {
        $debug = config('app.debug');

        return [
            // 'background' => '#eee', // 可以使用图片, 'url(http://xxxx)'
            'login_api' => route('admin.login'),
            'success_redirect' => route('admin.layout'),
            'form' => [
                Amis\FormField::make()->name('username')->label('用户名')->required()->value($debug ? 'admin' : ''),
                Amis\FormField::make()->name('password')->label('密码')->typeInputPassword()->required()->value($debug ? '123456' : ''),
            ],
        ];
    },
    /*
     * 用于全局替换组件的默认参数
     * @see Component::$config
     */
    'components' => [
        // 例如: 将列表页的字段默认左显示
        /*\WebmanTech\AmisAdmin\Amis\GridColumn::class => [
            'schema' => [
                'align' => 'left',
            ],
        ],*/
    ],
    /*
     * 默认的验证器
     * 返回一个 \WebmanTech\AmisAdmin\Validator\ValidatorInterface
     */
    'validator' => fn () => new LaravelValidator(Validator::instance()),
];
