<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

$redisHost = get_env('REDIS_HOST', '127.0.0.1');
$redisPassword = get_env('REDIS_PASSWORD');
$redisPort = (int)get_env('REDIS_PORT', 6379);
$redisDefaultDB = (int)get_env('REDIS_DB_DEFAULT', 0);
$redisCommonPrefix = config('app.name');

// Connection pool, supports only Swoole or Swow drivers.
$pool = [
    'max_connections' => 5,
    'min_connections' => 1,
    'wait_timeout' => 3,
    'idle_timeout' => 60,
    'heartbeat_interval' => 50,
];

return [
    'default' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => $redisDefaultDB,
        'prefix' => get_env('REDIS_PREFIX_DEFAULT', $redisCommonPrefix . ':redis:'),
        'pool' => $pool,
    ],
    // used by session.php
    'session' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_SESSION', $redisDefaultDB),
        'prefix' => get_env('REDIS_PREFIX_SESSION', $redisCommonPrefix. ':session:'),
        'pool' => $pool,
    ],
    // used by config/plugin/webman-tech/laravel-cache/cache.php
    'cache' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_CACHE', $redisDefaultDB),
        'prefix' => get_env('REDIS_PREFIX_CACHE', $redisCommonPrefix . ':cache:'),
        'pool' => $pool,
    ],
    // used by config/plugin/webman-tech/laravel-cache/cache.php
    'cache_lock' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_CACHE_LOCK', $redisDefaultDB),
        'prefix' => get_env('REDIS_PREFIX_CACHE_LOCK', $redisCommonPrefix . ':cache_lock:'),
        'pool' => $pool,
    ],
    // used by config/plugin/webman-tech/laravel-cache/rate_limiter.php
    'cache_limiter' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_CACHE_LIMITER', $redisDefaultDB),
        'prefix' => get_env('REDIS_PREFIX_CACHE_LIMITER', $redisCommonPrefix . ':cache_limiter:'),
        'pool' => $pool,
    ],
    // used by config/plugin/webman/redis-queue/redis.php
    'queue' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_QUEUE', $redisDefaultDB),
        'prefix' => get_env('REDIS_PREFIX_QUEUE', $redisCommonPrefix . ':queue:'),
        'pool' => $pool,
    ],
];
