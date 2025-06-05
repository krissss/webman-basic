<?php

return [
    /**
     * @see \WebmanTech\LaravelConsole\Kernel::$config
     */
    // 自定义 Command
    'commands' => [
        // commandName
    ],
    'commands_scan' => [
        'webman' => true, // 是否扫描 webman/console
        'illuminate_database' => false, // 是否扫描 illuminate/database
    ],
];
