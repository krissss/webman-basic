<?php

namespace process;

use Workerman\Crontab\Crontab;

/**
 * 需要安装依赖：composer require workerman/crontab
 * 使用见：
 * @link https://www.workerman.net/doc/webman/components/crontab.html
 * 开启请修改 .env CRONTAB_ENABLE="1"
 */
class Task
{
    public function onWorkerStart()
    {
        // 每分钟的第一秒执行
        new Crontab('1 * * * * *', [Task\TestTask::class, 'consume']);
    }
}
