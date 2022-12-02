<?php

namespace app\components;

use app\exception\UserSeeException;
use Psr\Log\LoggerInterface;
use support\facade\Logger;
use support\Log;
use Throwable;

abstract class BaseTask
{
    /**
     * 日志 channel
     * @var string
     */
    protected string $logChannel = Logger::CHANNEL_CRON_TASK;

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
            $self->log($e->getMessage(), 'warning');
            return;
        } catch (Throwable $e) {
            $self->log($e, 'error');
            throw $e;
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
        $msg = static::class . ':' . $msg;

        $this->logger->{$type}($msg);
    }
}
