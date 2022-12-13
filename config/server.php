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

return [
    'listen' => get_env('SERVER_LISTEN', 'http://0.0.0.0:8787'),
    'transport' => 'tcp',
    'context' => [],
    'name' => get_env('SERVER_NAME', config('app.name')),
    'count' => get_env('SERVER_COUNT', fn() => cpu_count() * 4),
    'user' => get_env('SERVER_USER', ''),
    'group' => get_env('SERVER_GROUP', ''),
    'reusePort' => false,
    'event_loop' => '',
    'stop_timeout' => 2,
    'pid_file' => runtime_path() . '/webman.pid',
    'status_file' => runtime_path() . '/webman.status',
    'stdout_file' => runtime_path() . '/logs/stdout.log',
    'log_file' => runtime_path() . '/logs/workerman.log',
    'max_package_size' => get_env('SERVER_MAX_PACKAGE_SIZE', 10) * 1024 * 1024, // 决定了上传文件的最大值
];
