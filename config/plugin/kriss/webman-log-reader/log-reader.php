<?php

return [
    // route
    'route' => [
        // 路由前缀
        'group' => get_env('LOG_READER_ROUTE', '/admin/log-reader'),
        // 路由中间件，可以用于控制访问权限
        'middleware' => [
            \app\middleware\SetAuthGuardAdmin::class,
            \app\middleware\AuthenticateAdmin::class,
        ],
    ],
    /**
     * 以下参数为 LogReader 的属性参数
     * @see Kriss\LogReader\LogReader
     */
    // 是否允许删除
    'deleteEnable' => get_env('LOG_READER_DELETE_ENABLE', false),
    // 日志根路径
    'logPath' => runtime_path() . '/logs',
    // tail 查看时默认读取的行大小
    'tailDefaultLine' => get_env('LOG_READER_TAIL_DEFAULT_LINE', 200),
];
