<?php

namespace app\command\framework;

use app\components\Component;
use app\model\Admin;
use Illuminate\Console\Command;

class InitDataCommand extends Command
{
    protected $signature = 'init-data';
    protected $description = '初始化数据';

    public function handle()
    {
        $this->info('start');

        // 此处增加的逻辑需要保证可以重复执行而不出错
        $this->initAdmin();

        $this->info('end');
    }

    private function initAdmin()
    {
        // Admin::destroy(Admin::SUPER_ADMIN_ID);
        if (Admin::query()->where('id', Admin::SUPER_ADMIN_ID)->exists()) {
            return;
        }

        $admin = new Admin([
            'id' => Admin::SUPER_ADMIN_ID,
            'username' => 'admin',
            'name' => '超级管理员',
        ]);
        $admin->password = Component::security()->generatePasswordHash('123456');
        $admin->refreshToken();
        $admin->save();
    }
}
