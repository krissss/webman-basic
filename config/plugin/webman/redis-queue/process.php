<?php

if (!get_env('QUEUE_CONSUMER_ENABLE', true)) {
    return [];
}

return [
    'consumer' => [
        'handler' => Webman\RedisQueue\Process\Consumer::class,
        'count' => get_env('QUEUE_REDIS_CONSUMER_COUNT', 8), // 可以设置多进程同时消费
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis'
        ]
    ]
];
