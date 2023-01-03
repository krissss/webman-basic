<?php

use app\admin\controller\AdminController;
use app\admin\controller\AuthController;
use app\admin\controller\FilesystemController;
use app\admin\controller\InfoController;
use app\admin\controller\SystemController;
use app\admin\controller\UserController;
use support\facade\Route;
use WebmanTech\AmisAdmin\Controller\RenderController;

// 以下路由定义不能使用 group 嵌套，会导致 middleware 丢失: https://github.com/walkor/webman-framework/issues/45
// 登录
Route::get('/login', [RenderController::class, 'login'])->name('admin.login.view');
Route::post('/auth/login', [AuthController::class, 'login'])->name('admin.login');
// 基础 layout
Route::get('', [RenderController::class, 'app'])->name('admin.layout');
Route::get('/pages', [SystemController::class, 'pages'])->name('admin.pages');
Route::get('/dashboard', [SystemController::class, 'dashboard'])->name('admin.dashboard.view');
Route::get('/iframe', [SystemController::class, 'iframe'])->name('admin.iframe.view');
// 个人信息
Route::get('/info-page', [InfoController::class, 'page'])->name('admin.info.view');
Route::get('/info', [InfoController::class, 'index'])->name('admin.info');
Route::post('/info/update', [InfoController::class, 'update'])->name('admin.info.update');
Route::post('/info/change-password', [InfoController::class, 'changePassword'])->name('admin.info.changePassword');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('admin.logout');
// crud
Route::resource('admin', AdminController::class, ['name_prefix' => 'admin.', 'resetPassword']);
Route::resource('user', UserController::class, ['name_prefix' => 'admin.', 'resetPassword', 'resetApiToken', 'login']);
Route::resource('filesystem', FilesystemController::class, [
    'name_prefix' => 'admin.',
    'uploadImage' => ['path' => '/{_name}/{_action}/{type}'],
    'uploadFile' => ['path' => '/{_name}/{_action}/{type}'],
    'url' => ['path' => '/{_name}/{_action}'],
]);
