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

use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

$redis = require __DIR__ . '/redis.php';
$sessionRedis = $redis['session'];

$sessionType = get_env('SESSION_ADAPTER', 'file');
$sessionTypeMap = [
    'file' => FileSessionHandler::class,
    'redis' => RedisSessionHandler::class,
    'redis_cluster' => RedisClusterSessionHandler::class,
];

return [

    'type' => $sessionType, // file or redis or redis_cluster

    'handler' => $sessionTypeMap[$sessionType],

    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        'redis' => [
            'host' => $sessionRedis['host'],
            'port' => $sessionRedis['port'],
            'auth' => $sessionRedis['password'],
            'timeout' => 2,
            'database' => $sessionRedis['database'],
            'prefix' => $sessionRedis['prefix'],
        ],
        'redis_cluster' => [
            'host' => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth' => '',
            'prefix' => $sessionRedis['prefix'],
        ]
    ],

    'session_name' => get_env('SESSION_NAME', 'WB_SID'),

    'auto_update_timestamp' => false,

    'lifetime' => get_env('SESSION_LIFETIME', 3*60*60),

    'cookie_lifetime' => get_env('SESSION_COOKIE_LIFETIME', 24*60*60),

    'cookie_path' => '/',

    'domain' => '',

    'http_only' => true,

    'secure' => false,

    'same_site' => '',

    'gc_probability' => [1, 1000],

];
