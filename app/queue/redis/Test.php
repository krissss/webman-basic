<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class Test implements Consumer
{
    // 要消费的队列名
    public $queue = 'test';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {
        // 无需反序列化
        var_export($data);
    }
}
