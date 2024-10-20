<?php

$redis = require __DIR__ . '/../../../redis.php';
$queueRedis = $redis['queue'];

return [
    'default' => [
        'host' => 'redis://' . $queueRedis['host'] . ':' . $queueRedis['port'],
        'options' => [
            'auth' => $queueRedis['password'],
            'db' => $queueRedis['database'],
            'prefix' => $queueRedis['prefix'],
            'max_attempts' => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ],
    ],
];
