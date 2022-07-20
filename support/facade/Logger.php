<?php

namespace support\facade;

/**
 * @method static void app($msg, string $type = 'info', array $context = [])
 * @method static void sql($msg, string $type = 'info', array $context = [])
 * @method static void appAdmin($msg, string $type = 'info', array $context = [])
 */
class Logger extends \Kriss\WebmanLogger\Logger
{
    /**
     * @param string $name
     * @param mixed $msg
     */
    public static function withChannel(string $name, $msg): void
    {
        static::{$name}($msg);
    }
}
