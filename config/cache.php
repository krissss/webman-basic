<?php

$cacheDefaultTTL = get_env('CACHE_DEFAULT_TTL', 3600);

return [
    'default' => get_env('CACHE_ADAPTER', 'file'),
    'drivers' => [
        'file' => [
            'driver' => 'file',
            'save_path' => runtime_path() . '/cache',
            'default_ttl' => $cacheDefaultTTL,
            'namespace' => 'webman',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'default_ttl' => $cacheDefaultTTL,
            'namespace' => '', // 已经通过 redis.config 下的 cache 限定 prefix
        ],
    ],
];
