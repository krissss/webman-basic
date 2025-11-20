<?php

use app\admin\controller;
use support\facade\Route;
use WebmanTech\AmisAdmin\Controller\RenderController;

// 以下路由定义不能使用 group 嵌套，会导致 middleware 丢失: https://github.com/walkor/webman-framework/issues/45
// 登录
Route::get('/login', [RenderController::class, 'login'])->name('admin.login.view');
Route::post('/auth/login', [controller\AuthController::class, 'login'])->name('admin.login');
// 基础 layout
Route::get('', [RenderController::class, 'app'])->name('admin.layout');
Route::get('/pages', [controller\SystemController::class, 'pages'])->name('admin.pages');
Route::get('/dashboard', [controller\SystemController::class, 'dashboard'])->name('admin.dashboard.view');
Route::get('/iframe', [controller\SystemController::class, 'iframe'])->name('admin.iframe.view');
// 个人信息
Route::get('/info-page', [controller\InfoController::class, 'page'])->name('admin.info.view');
Route::get('/info', [controller\InfoController::class, 'index'])->name('admin.info');
Route::post('/info/update', [controller\InfoController::class, 'update'])->name('admin.info.update');
Route::post('/info/change-password', [controller\InfoController::class, 'changePassword'])->name('admin.info.changePassword');
Route::post('/auth/logout', [controller\AuthController::class, 'logout'])->name('admin.logout');
// crud
Route::resource('admin', controller\AdminController::class, [
    'name_prefix' => 'admin.',
    'resetPassword' => ['method' => ['get', 'post'], 'path' => '/{_name}/services/{_action}/{id}'],
]);
Route::resource('user', controller\UserController::class, ['name_prefix' => 'admin.', 'resetPassword', 'resetApiToken', 'login']);
Route::resource('filesystem', controller\FilesystemController::class, [
    'name_prefix' => 'admin.',
    'uploadImage' => ['path' => '/{_name}/{_action}/{type}'],
    'uploadFile' => ['path' => '/{_name}/{_action}/{type}'],
    'url' => ['path' => '/{_name}/{_action}'],
]);
