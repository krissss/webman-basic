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

use Webman\Route;

Route::any('/', fn() => 'Hello!');

Route::group('/admin', function () {
    Route::post('/auth/login', [app\admin\controller\AuthController::class, 'login']);
});
Route::group('/admin', function () {
    Route::get('/info', [app\admin\controller\InfoController::class, 'index']);
    Route::post('/auth/logout', [app\admin\controller\AuthController::class, 'logout']);
})->middleware([
    app\middleware\AuthenticateAdmin::class,
]);

// 关闭默认路由
Route::disableDefaultRoute();
