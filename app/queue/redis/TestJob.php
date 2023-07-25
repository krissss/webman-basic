<?php

namespace app\queue\redis;

use app\components\BaseConsume;
use app\exception\UserSeeException;

class TestJob extends BaseConsume
{
    public $queue = 'test';

    /**
     * {@inheritdoc}
     */
    protected function handle($data)
    {
        // 无需反序列化
        var_export($data);
        if (random_int(1, 100) > 50) {
            throw new UserSeeException('该异常会停止消费，并记录 warning 日志');
        }
        if (random_int(1, 100) > 50) {
            throw new \Exception('其他异常会抛出异常，导致重试');
        }
        $this->log('这个可以记录 info 日志');
        // 正常结束无需返回
    }
}
