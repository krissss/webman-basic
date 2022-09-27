<?php

return [
    'enable' => true,
    /**
     * 日志相关
     */
    'log' => [
        /**
         * 日志是否启用，建议启用
         */
        'enable' => get_env('HTTP_CLIENT_LOG_ENABLE', true),
        /**
         * 日志的 channel
         */
        'channel' => \support\facade\Logger::CHANNEL_HTTP_CLIENT,
        /**
         * 日志的级别
         */
        'level' => 'info',
        /**
         * 日志格式
         * 启用 custom 时无实际作用
         * @link \GuzzleHttp\MessageFormatter::format()
         */
        'format' => \GuzzleHttp\MessageFormatter::CLF,
        /**
         * 自定义日志
         *
         * 返回 WebmanTech\LaravelHttpClient\Guzzle\Log\CustomLogInterface 时使用 @see WebmanTech\LaravelHttpClient\Guzzle\Log\Middleware::__invoke()
         * 返回 null 时使用 guzzle 的 @see GuzzleHttp\Middleware::log()
         * 返回 callable 时使用自定义 middleware @link https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware
         *
         * 建议使用 CustomLogInterface 形式，支持慢请求、请求时长、更多配置
         */
        'custom' => function (array $config) {
            /**
             * @see \WebmanTech\LaravelHttpClient\Guzzle\Log\CustomLog::$config
             */
            $config = [
                'filter_all' => get_env('HTTP_CLIENT_LOG_FILTER_ALL', false),
                'filter_2xx' => get_env('HTTP_CLIENT_LOG_FILTER_2XX', true),
                'filter_3xx' => get_env('HTTP_CLIENT_LOG_FILTER_3XX', true),
                'filter_4xx' => get_env('HTTP_CLIENT_LOG_FILTER_4XX', true),
                'filter_5xx' => get_env('HTTP_CLIENT_LOG_FILTER_5XX', true),
                'filter_slow' => get_env('HTTP_CLIENT_LOG_FILTER_SLOW', 1.0),
                'log_channel' => $config['channel'],
            ];
            return new \WebmanTech\LaravelHttpClient\Guzzle\Log\CustomLog($config);
        }
    ],
    /**
     * guzzle 全局的 options
     * @link https://laravel.com/docs/8.x/http-client#guzzle-options
     */
    'guzzle' => [
        'debug' => false,
        'timeout' => get_env('HTTP_CLIENT_LOG_DEFAULT_TIMEOUT', 10),
    ],
    /**
     * 扩展 Http 功能，一般可用于快速定义 api 信息
     * @link https://laravel.com/docs/8.x/http-client#macros
     */
    'macros' => \support\facade\Http::getAllMacros(),
];
