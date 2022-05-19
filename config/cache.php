<?php

$cacheDefaultTTL = get_env('CACHE_DEFAULT_TTL', 3600);
$cacheNamespace = get_env('CACHE_NAMESPACE', 'webman');

return [
    'default' => get_env('CACHE_ADAPTER', 'file'),
    'drivers' => [
        'file' => [
            'driver' => 'file',
            'save_path' => runtime_path() . '/cache',
            'default_ttl' => $cacheDefaultTTL,
            'namespace' => $cacheNamespace,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'default_ttl' => $cacheDefaultTTL,
            'namespace' => $cacheNamespace,
        ],
    ],
];
