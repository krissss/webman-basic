<?php

namespace support\facade;

use ReflectionClass;
use Yiisoft\Strings\StringHelper;

/**
 * @method static void app($msg, string $type = 'info', array $context = [])
 * @method static void sql($msg, string $type = 'info', array $context = [])
 * @method static void appAdmin($msg, string $type = 'info', array $context = [])
 * @method static void nacos($msg, string $type = 'info', array $context = [])
 * @method static void operateLog($msg, string $type = 'info', array $context = [])
 */
class Logger extends \Kriss\WebmanLogger\Logger
{
    public const CHANNEL_APP = 'app';
    public const CHANNEL_SQL = 'sql';
    public const CHANNEL_APP_ADMIN = 'appAdmin';
    public const CHANNEL_NACOS = 'nacos';
    public const CHANNEL_OPERATE_LOG = 'operateLog';

    /**
     * 所有日志通道
     * @return array
     */
    public static function getAllChannels(): array
    {
        $obj = new ReflectionClass(self::class);
        return array_unique(
            array_values(
                array_filter($obj->getConstants(), fn($name) => StringHelper::startsWith($name, 'CHANNEL_'), ARRAY_FILTER_USE_KEY)
            )
        );
    }

    /**
     * 特殊日志通道的等级，大于该等级的日志才记录
     * @return array
     */
    public static function getSpecialLevel(): array
    {
        return [
            //'channelName' => 'info',
        ];
    }

    /**
     * @param string $name
     * @param mixed $msg
     */
    public static function withChannel(string $name, $msg): void
    {
        static::{$name}($msg);
    }
}
