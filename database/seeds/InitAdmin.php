<?php


use app\model\Admin;
use Phinx\Seed\AbstractSeed;
use support\Security;

class InitAdmin extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        //Admin::destroy(Admin::SUPER_ADMIN_ID);
        if (Admin::exists(['id' => Admin::SUPER_ADMIN_ID])) {
            return;
        }

        $data = [
            'id' => Admin::SUPER_ADMIN_ID,
            'username' => 'admin',
            'password' => Security::generatePasswordHash(123456),
            'name' => '超级管理员',
        ];
        $this->table('admin')->insert($data)->save();
    }
}
