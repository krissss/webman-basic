<?php
return [
    'default' => [
        'host' => 'redis://' . get_env('REDIS_HOST', '127.0.0.1') . ':' . get_env('REDIS_PORT', 6379),
        'options' => [
            'auth' => get_env('REDIS_PASSWORD'),       // 密码，字符串类型，可选参数
            'db' => get_env('REDIS_DB_QUEUE', 0),            // 数据库
            'prefix' => get_env('REDIS_PREFIX_QUEUE', config('app.name') . ':queue:'),
            'max_attempts' => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
];
