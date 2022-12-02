<?php

namespace app\components;

use app\exception\UserSeeException;
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
    /**
     * 是否记录 class
     * 如果 logChannel 是独立的，可以选择关闭
     * @var bool
     */
    protected bool $logClass = true;

    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Log::channel($this->logChannel);
    }

    public function consume($data)
    {
        $this->log('start: ' . Json::encode($data));

        try {
            $this->handle($data);
        } catch (UserSeeException $e) {
            $this->log('UserSeeException:' . $e->getMessage() . ($e->getData() ? Json::encode($e->getData()) : ''), 'warning');
            return;
        } catch (Throwable $e) {
            $this->log($e, 'error');
            throw $e;
        }

        $this->log('end');
    }

    /**
     * @param $data
     * @return void
     * @throws UserSeeException
     */
    abstract protected function handle($data);

    /**
     * @param string $msg
     * @param string $type
     * @return void
     */
    protected function log(string $msg, string $type = 'info'): void
    {
        if ($this->logClass) {
            $msg = static::class . ':' . $msg;
        }
        $this->logger->{$type}($msg);
    }
}

