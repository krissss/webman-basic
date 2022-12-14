<?php

/**
 * @link https://github.com/laravel/laravel/blob/8.x/config/cache.php
 */
return [
    'default' => get_env('CACHE_ADAPTER', 'file'),
    'stores' => [
        'apc' => [
            'driver' => 'apc',
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache'),
        ],
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => get_env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                get_env('MEMCACHED_USERNAME'),
                get_env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => get_env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => get_env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'cache_lock',
            'limiter_connection' => 'cache_limiter',
        ],
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => get_env('AWS_ACCESS_KEY_ID'),
            'secret' => get_env('AWS_SECRET_ACCESS_KEY'),
            'region' => get_env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => get_env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => get_env('DYNAMODB_ENDPOINT'),
        ],
        'octane' => [
            'driver' => 'octane',
        ],
    ],
    //'prefix' => get_env('CACHE_PREFIX', Str::slug(config('app.name', 'webman'), '_').'_cache'),
    'prefix' => '', // 已经通过 redis.config 下的 cache 限定 prefix
    'extend' => null,
];
