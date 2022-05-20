<?php

namespace app\components;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Log\LogLevel;
use support\Log;
use Yiisoft\Json\Json;

/**
 * @method static void app($msg, string $type = 'info', array $context = [])
 * @method static void sql($msg, string $type = 'info', array $context = [])
 */
final class Logger extends BaseEnum
{
    private const APP = 'app';
    private const SQL = 'sql';

    public static function getLogConfig(): array
    {
        $channelNames = self::getValues();

        $channelConfigs = [];
        $processors = [
            new PsrLogMessageProcessor('Y-m-d H:i:s'),
            new Logger\Processors\WebRouteProcessor(),
            new Logger\Processors\WebUserProcessor(),
            new UidProcessor(),
        ];
        foreach ($channelNames as $channelName) {
            $channelConfigs[$channelName] = [
                'handlers' => self::defaultChannelHandlers($channelName),
                'processors' => $processors,
            ];
        }

        return $channelConfigs;
    }

    private static function defaultChannelHandlers($channelName): array
    {
        $handlers = [];
        if (config('log-channel.mode.split', true)) {
            $handlers[] = self::channelRotatingFileHandler($channelName, Logger\Formatter\ChannelFormatter::class);
        }
        if (config('log-channel.mode.mix', false)) {
            if (strpos(config('log-channel.mode.mix_skip', ''), ",{$channelName},") === false) {
                $handlers[] = self::channelRotatingFileHandler(
                    config('log-channel.mode.mix_name', 'channelMixed'),
                    Logger\Formatter\ChannelMixedFormatter::class
                );
            }
        }
        return $handlers;
    }

    private static function channelRotatingFileHandler($channelName, string $formatterClass): array
    {
        $levelSpec = [
            self::SQL => config('log-channel.sql.level', LogLevel::INFO),
        ];
        return [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                runtime_path() . "/logs/{$channelName}/{$channelName}.log",
                config('log-channel.max_files', 0),
                $levelSpec[$channelName] ?? (config('app.debug') ? LogLevel::DEBUG : LogLevel::INFO),
            ],
            'formatter' => [
                'class' => $formatterClass,
                'constructor' => [],
            ],
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $level = $arguments[1] ?? 'info';
        $context = $arguments[2] ?? [];
        Log::channel($name)->log($level, self::formatMessage($arguments[0]), (array)$context);
    }

    private static function formatMessage($message)
    {
        if (is_array($message)) {
            $message = Json::encode($message);
        }

        return $message;
    }
}
