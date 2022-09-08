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

Route::any('/', fn() => 'Hello ' . config('app.name'));

Route::group('/admin', function () {
    require __DIR__ . '/../app/admin/route.php';
})->middleware([
    app\middleware\SetAuthGuardAdmin::class,
    app\middleware\AuthenticateAdmin::class,
]);
Route::group('/user', function () {
    require __DIR__ . '/../app/user/route.php';
})->middleware([
    app\middleware\AmisModuleChange2User::class,
    app\middleware\AuthenticateUser::class,
]);
Route::group('/api', function () {
    require __DIR__ . '/../app/api/route.php';
});

// 关闭默认路由
Route::disableDefaultRoute();
