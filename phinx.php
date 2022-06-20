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

require_once __DIR__ . '/support/bootstrap.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => get_env('DB_MYSQL_HOST'),
            'name' => get_env('DB_MYSQL_DATABASE'),
            'user' => get_env('DB_MYSQL_USERNAME'),
            'pass' => get_env('DB_MYSQL_PASSWORD'),
            'port' => get_env('DB_MYSQL_PORT'),
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation',
    'migration_base_class' => app\components\BaseMigration::class,
];
