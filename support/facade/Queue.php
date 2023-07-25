<?php

namespace support\facade;

use Webman\RedisQueue\Client;
use Webman\RedisQueue\Redis;

class Queue
{
    /**
     * 同步投敌消息.
     */
    public static function send(string $queue, array $data = [], int $delay = 0, string $connection = null): bool
    {
        $connection ??= 'default';

        return Redis::connection($connection)->send($queue, $data, $delay);
    }

    /**
     * 异步投敌消息.
     */
    public static function sendAsync(string $queue, array $data = [], int $delay = 0, string $connection = null): void
    {
        if (!\function_exists('pcntl_alarm')) {
            // windows 不支持异步分发，自动切换成同步
            static::send($queue, $data, $delay, $connection);

            return;
        }

        $connection ??= 'default';
        Client::connection($connection)->send($queue, $data, $delay);
    }

    /**
     * 同步分发任务
     */
    public static function dispatch(string $consumerClass, array $data = [], int $delay = 0): bool
    {
        [$connection, $queue] = static::getInfoFromConsumer($consumerClass);

        return static::send($queue, $data, $delay, $connection);
    }

    /**
     * 异步分发任务
     */
    public static function dispatchAsync(string $consumerClass, array $data = [], int $delay = 0): void
    {
        [$connection, $queue] = static::getInfoFromConsumer($consumerClass);
        static::sendAsync($queue, $data, $delay, $connection);
    }

    /**
     * 从 consumer 类中获取信息.
     *
     * @see \Webman\RedisQueue\Process\Consumer::onWorkerStart
     */
    protected static function getInfoFromConsumer(string $consumerClass): array
    {
        if (!is_a($consumerClass, 'Webman\RedisQueue\Consumer', true)) {
            throw new \InvalidArgumentException($consumerClass.' 必须是 Webman\RedisQueue\Consumer');
        }

        $consumer = Container::get($consumerClass);
        $connection = $consumer->connection ?? 'default';
        $queue = $consumer->queue ?? 'default';

        return [$connection, $queue];
    }
}
