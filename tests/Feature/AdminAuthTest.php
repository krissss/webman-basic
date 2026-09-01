<?php

use app\components\Component;
use app\enums\AdminStatusEnum;
use app\model\Admin;

// 数据隔离由基类 setUpDatabase() truncate 模式每测试清空 admin 表（config/testing.php database.truncate）
function seedAdmin(string $username, string $password): void
{
    Admin::query()->forceCreate([
        'username' => $username,
        // password 不在 fillable，forceCreate 绕过批量赋值保护
        'password' => Component::security()->generatePasswordHash($password),
        'name' => 'Admin',
        'status' => AdminStatusEnum::Enabled->value,
    ]);
}

test('admin can login and access protected info with token', function () {
    seedAdmin('admin', 'secret');

    $response = $this->postJson('/admin/auth/login', ['username' => 'admin', 'password' => 'secret']);
    $response->assertOk();
    $token = $response->json('data.access_token');
    expect($token)->toBeString()->not->toBeEmpty();

    $this->withToken($token)->getJson('/admin/info')->assertOk();
});

test('admin login rejects wrong password', function () {
    seedAdmin('admin', 'secret');

    $response = $this->postJson('/admin/auth/login', ['username' => 'admin', 'password' => 'wrong']);
    $response->assertOk();
    expect($response->json('status'))->toBe(422)
        ->and($response->json('errors.username'))->toBe('用户名或密码错误');
});
