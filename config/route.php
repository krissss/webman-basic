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
use WebmanTech\AmisAdmin\Middleware\AmisModuleChangeMiddleware;
use WebmanTech\Auth\Middleware\SetAuthGuard;

Route::any('/', fn() => 'Hello ' . config('app.name'));

Route::group('/admin', function () {
    require __DIR__ . '/../app/admin/route.php';
})->middleware([
    fn() => new SetAuthGuard('admin'),
    app\middleware\AuthenticateAdmin::class,
]);
Route::group('/user', function () {
    require __DIR__ . '/../app/user/route.php';
})->middleware([
    fn() => new AmisModuleChangeMiddleware('amis-user'),
    app\middleware\AuthenticateUser::class,
]);
Route::group('/api', function () {
    require __DIR__ . '/../app/api/route.php';
});

// 关闭默认路由
Route::disableDefaultRoute();
