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

use support\Request;

$appDebug = !!get_env('APP_DEBUG', false);

return [
    'debug' => $appDebug,
    'error_reporting' => get_env('APP_ERROR_REPORTING', $appDebug ? E_ALL : E_ERROR | E_PARSE),
    'default_timezone' => get_env('APP_TIME_ZONE', 'Asia/Shanghai'),
    'request_class' => Request::class,
    'public_path' => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path' => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix' => 'Controller',
    'controller_reuse' => false, // 为了避免控制器中的变量缓存问题，一定不启用
    'name' => get_env('APP_NAME', 'webman_basic'),
];
