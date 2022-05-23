<?php

namespace support\facade;

/**
 * @method static void app($msg, string $type = 'info', array $context = [])
 * @method static void sql($msg, string $type = 'info', array $context = [])
 */
class Logger extends \app\components\Logger
{
    private const APP = 'app';
    private const SQL = 'sql';
}
