<?php

namespace app\components;

use Phinx\Db\Table;
use Phinx\Migration\AbstractMigration;

abstract class BaseMigration extends AbstractMigration
{
    /**
     * 添加常用的列.
     */
    public function addCommonColumns(Table $table, array $include = []): void
    {
        $columns = [
            'sort' => fn () => $table->addColumn('sort', 'integer', ['comment' => '排序', 'default' => 10]),
            'status' => fn () => $table->addColumn('status', 'integer', ['comment' => '状态', 'default' => 0]),
            'created_at' => fn () => $table->addColumn('created_at', 'timestamp', ['comment' => '创建时间', 'default' => 'CURRENT_TIMESTAMP']),
            'updated_at' => fn () => $table->addColumn('updated_at', 'timestamp', ['comment' => '修改时间', 'default' => 'CURRENT_TIMESTAMP'/* , 'update' => 'CURRENT_TIMESTAMP' */]),
            'deleted_at' => fn () => $table->addColumn('deleted_at', 'timestamp', ['comment' => '删除时间', 'null' => true]),
            'created_by' => fn () => $table->addColumn('created_by', 'integer', ['comment' => '创建人', 'default' => 0]),
            'updated_by' => fn () => $table->addColumn('created_by', 'integer', ['comment' => '修改人', 'default' => 0]),
        ];
        foreach ($include as $key) {
            \call_user_func($columns[$key]);
        }
    }
}
