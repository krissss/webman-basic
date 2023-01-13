<?php

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
    'channels' => \support\facade\Notification::channels(),
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
];
