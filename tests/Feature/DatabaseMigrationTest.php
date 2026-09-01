<?php

use app\components\Component;
use app\enums\AdminStatusEnum;
use app\model\Admin;

test('feature tests run migrated sqlite database', function () {
    $this->assertDatabaseHas('phinxlog', ['migration_name' => 'CreateAdmin']);
    $this->assertDatabaseHas('phinxlog', ['migration_name' => 'CreateUser']);
});

test('test process and server process share the same sqlite file', function () {
    // 测试进程直连写入，server 进程（同一文件库）可查询到
    Admin::query()->forceCreate([
        'username' => 'shared_admin',
        // password 不在 fillable，forceCreate 绕过批量赋值保护
        'password' => Component::security()->generatePasswordHash('secret'),
        'name' => 'Shared Admin',
        'status' => AdminStatusEnum::Enabled->value,
    ]);

    $this->assertDatabaseHas('admin', ['username' => 'shared_admin']);
});
