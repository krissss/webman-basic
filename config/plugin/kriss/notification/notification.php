<?php

use WebmanTech\Logger\Middleware\RequestUid;

return [
    /**
     * 默认渠道
     */
    'default' => \support\facade\Notification::CHANNEL_DEFAULT,
    /**
     * 所有渠道定义
     * key 为渠道的 name
     * class 是渠道的 Channel 类
     * 其他配置见 Channel 类下的 $config 参数
     */
    'channels' => fn() => \support\facade\Notification::channels(),
    /**
     * 日志相关
     */
    'log' => [
        /**
         * 是否启用日志
         */
        'enable' => true,
        /**
         * PSR3 LoggerInterface 的实现
         * callable|null
         */
        'instance' => null,
        /**
         * 渠道，当 instance 为 null 时，不同框架下可以用于切换渠道
         */
        'channel' => \support\facade\Logger::CHANNEL_NOTIFICATION,
    ],
    /**
     * 缓存相关
     */
    'cache' => [
        /**
         * PSR16 CacheInterface 的实现
         * callable|null
         */
        'instance' => fn() => \support\facade\Cache::psr16(),
        /**
         * 驱动，当 instance 为 null 时，不同框架下可以用于切换驱动
         */
        'driver' => null,
    ],
    /**
     * 异常相关
     */
    'exception' => [
        /**
         * 接管异常处理
         * callable|null|className
         */
        'handler' => null,
        /**
         * 当 handler 为 null 时，是否抛出异常
         */
        'throw' => config('app.debug', true),
    ],
    /**
     * 模版相关
     */
    'template' => [
        /**
         * 统一处理模版的 toString
         * callable|null|className
         */
        'handler' => \Kriss\Notification\Integrations\Webman\TemplateHandler\EnvTemplateHandler::class,
        /**
         * 用于 handler 的配置信息
         */
        'extra_info' => [
            'uid' => fn() => request() ? request()->{RequestUid::REQUEST_UID_KEY} : 'console'
        ],
    ],
];
