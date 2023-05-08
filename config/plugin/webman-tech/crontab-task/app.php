<?php

use support\facade\Logger;

return [
    'enable' => get_env('CRONTAB_ENABLE', false),
    'log' => [
        /**
         * @see \WebmanTech\CrontabTask\BaseTask
         * @see \WebmanTech\CrontabTask\Tasks\SampleTask
         */
        'channel' => Logger::CHANNEL_CRON_TASK,
        'level' => 'info',
        'log_class' => true,
    ],
];
