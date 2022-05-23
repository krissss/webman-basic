<?php

namespace app\components;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Log\LogLevel;
use support\Log;
use Yiisoft\Json\Json;

/**
 * @internal 使用 support\facade\Logger
 */
class Logger extends BaseEnum
{
    public static function getLogConfig(): array
    {
        $channelNames = static::getValues();

        $channelConfigs = [];
        $processors = [
            new PsrLogMessageProcessor('Y-m-d H:i:s'),
            new Logger\Processors\WebRouteProcessor(),
            new Logger\Processors\WebUserProcessor(),
            new UidProcessor(),
        ];
        foreach ($channelNames as $channelName) {
            $channelConfigs[$channelName] = [
                'handlers' => static::defaultChannelHandlers($channelName),
                'processors' => $processors,
            ];
        }

        return $channelConfigs;
    }

    protected static function defaultChannelHandlers($channelName): array
    {
        $handlers = [];
        if (config('log-channel.mode.split', true)) {
            $handlers[] = static::channelRotatingFileHandler($channelName, Logger\Formatter\ChannelFormatter::class);
        }
        if (config('log-channel.mode.mix', false)) {
            if (strpos(config('log-channel.mode.mix_skip', ''), ",{$channelName},") === false) {
                $handlers[] = static::channelRotatingFileHandler(
                    config('log-channel.mode.mix_name', 'channelMixed'),
                    Logger\Formatter\ChannelMixedFormatter::class
                );
            }
        }
        return $handlers;
    }

    protected static function channelRotatingFileHandler($channelName, string $formatterClass): array
    {
        $levelSpec = config('log-channel.level');
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
        Log::channel($name)->log($level, static::formatMessage($arguments[0]), (array)$context);
    }

    private static function formatMessage($message)
    {
        if (is_array($message)) {
            $message = Json::encode($message);
        }

        return $message;
    }
}
