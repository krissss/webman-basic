<?php

namespace app\components;

use app\exception\UserSeeException;
use Psr\Log\LoggerInterface;
use support\facade\Logger;
use support\Log;
use Throwable;
use Yiisoft\Json\Json;

abstract class BaseTask
{
    /**
     * 日志 channel
     * @var string
     */
    protected string $logChannel = Logger::CHANNEL_CRON_TASK;
    /**
     * 是否记录 class
     * 如果 logChannel 是独立的，可以选择关闭
     * @var bool
     */
    protected bool $logClass = true;

    protected LoggerInterface $logger;

    final public function __construct()
    {
        $this->logger = Log::channel($this->logChannel);
    }

    /**
     * 定时任务的入口
     * @return void
     * @throws Throwable
     */
    public static function consume()
    {
        $self = new static();

        $self->log('start');

        try {
            $self->handle();
        } catch (UserSeeException $e) {
            $self->log('UserSeeException:' . $e->getMessage() . ($e->getData() ? Json::encode($e->getData()) : ''), 'warning');
            return;
        } catch (Throwable $e) {
            $self->log($e, 'error');
            return;
        }

        $self->log('end');
    }

    /**
     * @return void.
     * @throws UserSeeException
     */
    abstract protected function handle();

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