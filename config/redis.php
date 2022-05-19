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
$redisPort = get_env('REDIS_PORT', 6379);

return [
    'default' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_DEFAULT', 0),
    ],
    'session' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_SESSION', 1),
    ],
    'cache' => [
        'host' => $redisHost,
        'password' => $redisPassword,
        'port' => $redisPort,
        'database' => get_env('REDIS_DB_CACHE', 2),
    ],
];
