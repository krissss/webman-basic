<?php

use app\model\Admin;
use WebmanTech\LaravelDatabase\Facades\DB;

test('feature tests run migrated sqlite database in transaction', function () {
    expect(DB::getSchemaBuilder()->hasTable('admin'))->toBeTrue()
        ->and(DB::getSchemaBuilder()->hasTable('user'))->toBeTrue();

    $admin = new Admin();
    $admin->forceFill([
        'username' => 'feature_admin',
        'password' => 'secret',
        'name' => 'Feature Admin',
        'status' => 1,
    ]);
    $admin->save();

    expect(Admin::query()->where('username', 'feature_admin')->exists())->toBeTrue();
});
