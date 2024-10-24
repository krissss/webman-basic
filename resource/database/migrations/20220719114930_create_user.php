<?php

declare(strict_types=1);

use app\components\BaseMigration;

final class CreateUser extends BaseMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('user', ['comment' => '用户表'])
            ->addColumn('username', 'string', ['comment' => '用户名', 'null' => false, 'limit' => 64])
            ->addColumn('password', 'string', ['comment' => '密码', 'null' => false, 'limit' => 100])
            ->addColumn('name', 'string', ['comment' => '名称', 'null' => false, 'limit' => 64])
            ->addColumn('access_token', 'string', ['comment' => 'Access Token', 'null' => true, 'limit' => 100])
            ->addColumn('api_token', 'string', ['comment' => 'Api Token', 'null' => true, 'limit' => 100]);
        $this->addCommonColumns($table, [
            'status', 'created_at', 'updated_at', 'deleted_at',
        ]);
        $table->addIndex(['username'], ['unique' => true]);
        $table->addIndex(['access_token'], ['unique' => true]);
        $table->create();
    }
}
