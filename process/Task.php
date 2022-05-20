<?php

namespace process;

use Workerman\Crontab\Crontab;

/**
 * 使用见：https://www.workerman.net/doc/webman/components/crontab.html
 * 开启青岛 config/process.php 中打开注释
 */
class Task
{
    public function onWorkerStart()
    {
        // 每分钟的第一秒执行
        new Crontab('1 * * * * *', [Task\Test::class, 'handle']);
    }
}
