<?php
return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => get_env('DB_MYSQL_HOST', '127.0.0.1'),
            'port'        => get_env('DB_MYSQL_PORT', '3306'),
            'database'    => get_env('DB_MYSQL_DATABASE', 'webman_basic'),
            'username'    => get_env('DB_MYSQL_USERNAME', 'root'),
            'password'    => get_env('DB_MYSQL_PASSWORD', 'root'),
            'unix_socket' => '',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options'   => [
                PDO::ATTR_EMULATE_PREPARES => false, // Must be false for Swoole and Swow drivers.
            ],
            // Connection pool, supports only Swoole or Swow drivers.
            'pool' => [
                'max_connections' => 5,
                'min_connections' => 1,
                'wait_timeout' => 3,
                'idle_timeout' => 60,
                'heartbeat_interval' => 50,
            ],
        ],
    ],
];
