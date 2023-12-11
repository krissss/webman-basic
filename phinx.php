<?php
/**
 * https://github.com/cakephp/phinx
 * https://tsy12321.gitbooks.io/phinx-doc/content/
 *
 * 常用命令：
 * 创建迁移：composer phinx create MyMigration
 * 执行迁移：composer phinx migrate
 * 回滚迁移：composer phinx rollback
 * 创建seed: composer phinx seed:create MySeed
 * 执行所有seed: composer phinx seed:run
 * 执行个别seed: vendor/bin/phinx seed:run -s MySeed
 */

use support\Db;

require_once __DIR__ . '/support/bootstrap.php';

$connectionName = config('database.default');

return [
    'paths' => [
        'migrations' => base_path('resource/database/migrations'),
        'seeds' => base_path('resource/database/seeds'),
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'name' => config("database.connections.{$connectionName}.database"),
            'connection' => Db::connection($connectionName)->getPdo(),
        ],
    ],
    'version_order' => 'creation',
    'migration_base_class' => app\components\BaseMigration::class,
];
