<?php

namespace app\command\framework;

use app\components\Component;
use app\model\Admin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDataCommand extends Command
{
    protected static $defaultName = 'init-data';
    protected static $defaultDescription = '初始化数据';

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 此处增加的逻辑需要保证可以重复执行而不出错
        $this->initAdmin();

        return self::SUCCESS;
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
