<?php

use app\admin\controller\AdminController;
use app\admin\controller\AuthController;
use app\admin\controller\InfoController;
use app\admin\controller\SystemController;
use app\middleware\AuthenticateAdmin;
use app\middleware\SetAuthGuardAdmin;
use Kriss\WebmanAmisAdmin\Controller\RenderController;
use Webman\Route;

// 不需要登录
Route::group('', function () {
    Route::get('/login', [RenderController::class, 'login'])->name('admin.login.view');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('admin.login');
})->middleware([
    SetAuthGuardAdmin::class,
]);

// 需要登录
Route::group('', function () {
    Route::get('', [RenderController::class, 'app'])->name('admin.layout');
    Route::get('/pages', [SystemController::class, 'pages'])->name('admin.pages');

    Route::get('/info', [InfoController::class, 'index']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::resource('/admin', AdminController::class);
    Route::post('/admin/reset-password/{id}', [AdminController::class, 'resetPassword'])->name('admin.reset-password');
})->middleware([
    SetAuthGuardAdmin::class,
    AuthenticateAdmin::class,
]);
