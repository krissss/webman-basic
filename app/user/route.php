<?php

use app\user\controller\AuthController;
use app\user\controller\InfoController;
use app\user\controller\SystemController;
use support\facade\Route;
use WebmanTech\AmisAdmin\Controller\RenderController;

// 以下路由定义不能使用 group 嵌套，会导致 middleware 丢失: https://github.com/walkor/webman-framework/issues/45
// 登录
Route::get('/login', [RenderController::class, 'login'])->name('user.login.view');
Route::post('/auth/login', [AuthController::class, 'login'])->name('user.login');
Route::get('/auth/login-admin/{accessToken}/{id}', [AuthController::class, 'loginByAdmin'])->name('user.login.admin');
// 基础 layout
Route::get('', [RenderController::class, 'app'])->name('user.layout');
Route::get('/pages', [SystemController::class, 'pages'])->name('user.pages');
Route::get('/dashboard', [SystemController::class, 'dashboard'])->name('user.dashboard.view');
Route::get('/iframe', [SystemController::class, 'iframe'])->name('user.iframe.view');
// 个人信息
Route::get('/info-page', [InfoController::class, 'page'])->name('user.info.view');
Route::get('/info', [InfoController::class, 'index'])->name('user.info');
Route::post('/info/update', [InfoController::class, 'update'])->name('user.info.update');
Route::post('/info/change-password', [InfoController::class, 'changePassword'])->name('user.info.changePassword');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('user.logout');
// crud
// Route::resource('admin', AdminController::class);
