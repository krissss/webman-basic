<?php

use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\RedisStore;

return [
    'storage' => get_env('LOCK_ADAPTER', 'redis'), // file/redis， file 不支持 ttl
    'storage_configs' => [
        'file' => [
            'class' => FlockStore::class,
            'construct' => [
                'lockPath' => runtime_path() . '/lock',
            ],
        ],
        'redis' => [
            'class' => RedisStore::class,
            'construct' => function() {
                return [
                    'redis' => \support\Redis::connection('default')->client(),
                ];
            },
        ],
    ],
];
