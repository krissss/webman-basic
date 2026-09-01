<?php
/**
 * webman-tech/testing 组件配置（测试进程与 webman 进程内 config('testing') 同源）
 * 说明见 vendor/webman-tech/testing/README.md
 */
return [
    'httpClient' => [
        'timeout' => 10,          // 请求超时（默认 10）
        'connect_timeout' => 2,   // 连接超时（默认 2）
    ],
    // 数据库就绪（tests/TestCase.php 的 setUpDatabase() 使用）
    'database' => [
        // 迁移器默认 phinx：复用根目录 phinx.php（environment 默认 development），无需额外配置
        'truncate' => ['admin', 'user'],  // 每测试清空业务表（迁移表 phinxlog 无需清理）
    ],
];
