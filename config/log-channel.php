<?php

return [
    'max_files' => get_env('LOG_CHANNEL_MAX_FILES', 0), // 最大文件数
    'mode' => [
        'split' => get_env('LOG_CHANNEL_MODE_SPLIT', true), // 分开存储
        'mix' => get_env('CHANNEL_LOG_MODE_MIX', false), // 合并存储
        'mix_name' => 'channelMixed', // 合并的文件名
        'mix_skip' => get_env('CHANNEL_LOG_MODE_MIX_SKIP', ''), // 合并存储时跳过哪些 channel，逗号分隔
    ],
    'sql' => [
        'enable' => get_env('LOG_CHANNEL_SQL_ENABLE', true), // 是否开启 sql 日志
        'level' => get_env('LOG_CHANNEL_SQL_LEVEL', 'info'), // sql 日志级别
        'warning_time' => get_env('LOG_CHANNEL_SQL_WARNING_TIME', 1500), // sql 执行时间超过多少毫秒记录为 warning
        'error_time' => get_env('LOG_CHANNEL_SQL_ERROR_TIME', 10000), // sql 执行时间超过多少毫秒记录为 error
    ],
];
