<?php

namespace app\queue\redis;

use app\components\BaseConsume;

class TestJob extends BaseConsume
{
    public $queue = 'test';

    /**
     * @inheritdoc
     */
    public function handle($data)
    {
        // 无需反序列化
        var_export($data);
    }
}
