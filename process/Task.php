<?php

namespace process;

/**
 * 需要安装依赖：composer require workerman/crontab
 * 使用见：
 * @link https://www.workerman.net/doc/webman/components/crontab.html
 * 开启请修改 .env CRONTAB_ENABLE="1"
 */
class Task
{
    /**
     * webman 的 crontab 会存在阻塞，sql 慢会阻塞后续的进程
     * @link https://github.com/walkor/crontab/issues/11
     * 所以将每个定时任务都定义为一个进程，此为定义进程的配置
     */
    public static function processes(): array
    {
        return [
            //'test' => Task\TestTask::class,
        ];
    }
}
