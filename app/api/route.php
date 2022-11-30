<?php

use app\api\controller\OpenApiController;
use support\facade\Route;
use WebmanTech\Auth\Middleware\Authentication;
use WebmanTech\Auth\Middleware\SetAuthGuard;

OpenApiController::registerRoute();

// 请勿将以下注释打开使用，仅做参考使用
//Route::resource('example', \app\api\controller\ExampleSourceController::class, ['name_prefix' => 'api.']);

Route::group('', function () {
    // 需要授权的
    Route::get('/info/mine', [\app\api\controller\InfoController::class, 'mine']);
})->middleware([
    fn() => new SetAuthGuard('api_user'),
    Authentication::class,
]);
