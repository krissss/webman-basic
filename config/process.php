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

use app\process\Http;
use support\Log;
use support\Request;

global $argv;

$httpProcessName = get_env('SERVER_NAME', config('app.name', 'webman'));

$processes = [
    $httpProcessName => [
        'handler' => Http::class,
        'listen' => get_env('SERVER_LISTEN', 'http://0.0.0.0:8787'),
        'count' => get_env('SERVER_COUNT', fn() => min(cpu_count() * 4, 12)),
        'user' => get_env('SERVER_USER', ''),
        'group' => get_env('SERVER_GROUP', ''),
        'reusePort' => false,
        'eventLoop' => '',
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ],
    // File update detection and automatic reload
    'monitor' => [
        'handler' => app\process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
                base_path() . '/env.php',
                base_path() . '/env.local.php',
                base_path() . '/vendor/webman-tech/components-monorepo',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];


if (!config('app.debug', false)) {
    unset($processes['monitor']);
}

return $processes;
