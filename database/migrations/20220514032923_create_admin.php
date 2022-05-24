<?php
declare(strict_types=1);

use App\components\BaseMigration;

final class CreateAdmin extends BaseMigration
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
        $table = $this->table('admin', ['comment' => '管理员表'])
            ->addColumn('username', 'string', ['limit' => 64])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('access_token', 'string', ['limit' => 255, 'null' => true]);
        $this->addCommonColumns($table, [
            'status', 'created_at', 'updated_at',
        ]);
        $table->addIndex(['username'], ['unique' => true]);
        $table->addIndex(['access_token'], ['unique' => true]);
        $table->create();
    }
}
