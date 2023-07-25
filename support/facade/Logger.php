<?php

namespace support\facade;

use Yiisoft\Strings\StringHelper;

/**
 * @method static void app($msg, string $type = 'info', array $context = [])
 * @method static void appAmis($msg, string $type = 'info', array $context = [])
 * @method static void appApi($msg, string $type = 'info', array $context = [])
 * @method static void sql($msg, string $type = 'info', array $context = [])
 * @method static void httpClient($msg, string $type = 'info', array $context = [])
 * @method static void queueJob($msg, string $type = 'info', array $context = [])
 * @method static void cronTask($msg, string $type = 'info', array $context = [])
 * @method static void nacos($msg, string $type = 'info', array $context = [])
 * @method static void operateLog($msg, string $type = 'info', array $context = [])
 * @method static void notification($msg, string $type = 'info', array $context = [])
 */
class Logger extends \WebmanTech\Logger\Logger
{
    public const CHANNEL_APP = 'app';
    public const CHANNEL_APP_AMIS = 'appAmis';
    public const CHANNEL_APP_API = 'appApi';
    public const CHANNEL_SQL = 'sql';
    public const CHANNEL_HTTP_CLIENT = 'httpClient';
    public const CHANNEL_QUEUE_JOB = 'queueJob';
    public const CHANNEL_CRON_TASK = 'cronTask';
    public const CHANNEL_NACOS = 'nacos';
    public const CHANNEL_OPERATE_LOG = 'operateLog';
    public const CHANNEL_NOTIFICATION = 'notification';

    /**
     * 所有日志通道.
     */
    public static function getAllChannels(): array
    {
        $obj = new \ReflectionClass(self::class);

        return array_unique(
            array_values(
                array_filter($obj->getConstants(), fn ($name) => StringHelper::startsWith($name, 'CHANNEL_'), \ARRAY_FILTER_USE_KEY)
            )
        );
    }

    /**
     * 特殊日志通道的等级，大于该等级的日志才记录.
     */
    public static function getSpecialLevel(): array
    {
        return [
            // 'channelName' => 'info',
        ];
    }

    /**
     * @param mixed $msg
     */
    public static function withChannel(string $name, $msg): void
    {
        static::{$name}($msg);
    }
}
