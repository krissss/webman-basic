<?php

namespace app\components;

use Psr\Log\LoggerInterface;
use support\facade\Logger;
use support\Log;
use Throwable;
use Webman\RedisQueue\Consumer;
use Yiisoft\Json\Json;

abstract class BaseConsume implements Consumer
{
    /**
     * 要消费的队列名
     * @var string
     */
    public $queue = 'default';
    /**
     * 连接名
     * 对应 plugin/webman/redis-queue/redis.php 里的连接
     * @var string
     */
    public $connection = 'default';
    /**
     * 日志 channel
     * @var string
     */
    protected string $logChannel = Logger::CHANNEL_QUEUE_JOB;

    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Log::channel($this->logChannel);
    }

    public function consume($data)
    {
        $this->logger->info('queue job start: ' . Json::encode($data));

        try {
            $this->handle($data);
        } catch (Throwable $e) {
            $this->logger->error('queue job exception: ' . $e);
            throw $e;
        }

        $this->logger->info('queue job over');
    }

    /**
     * @param $data
     * @return mixed
     */
    abstract protected function handle($data);
}

