<?php

use support\facade\Auth;
use support\facade\Logger;
use WebmanTech\Logger\Formatter\ChannelFormatter;
use WebmanTech\Logger\Formatter\ChannelMixedFormatter;
use WebmanTech\Logger\Processors\RequestRouteProcessor;
use WebmanTech\Logger\Processors\RequestUidProcessor;
use WebmanTech\Logger\Processors\CurrentUserProcessor;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    // channels
    'channels' => Logger::getAllChannels(),
    // 记录等级，仅大于设定等级的日志才会真实写入日志文件
    'levels' => [
        // 默认等级
        'default' => config('app.debug') ? 'debug' : 'info',
        // 特殊的等级
        'special' => Logger::getSpecialLevel(),
    ],
    // processors
    'processors' => function () {
        return [
            new PsrLogMessageProcessor('Y-m-d H:i:s', true),
            new RequestRouteProcessor(),
            new CurrentUserProcessor(function () {
                // 返回当前用户id
                if ($guard = Auth::guard()) {
                    return $guard->getId() ?: 0;
                }
                return 0;
            }),
            new RequestUidProcessor(),
        ];
    },
    // 模式
    'modes' => [
        // 按照channel分目录记录
        'split' => [
            'class' => WebmanTech\Logger\Mode\SplitMode::class,
            'enable' => get_env('LOG_CHANNEL_MODE_SPLIT', true),
            'except_channels' => [],
            'only_channels' => [],
            'formatter' => [
                'class' => ChannelFormatter::class,
            ],
            'max_files' => get_env('LOG_CHANNEL_MAX_FILES', 30), // 最大文件数
        ],
        // 将所有channel合并到一起记录
        'mix' => [
            'class' => WebmanTech\Logger\Mode\MixMode::class,
            'enable' => get_env('CHANNEL_LOG_MODE_MIX', false),
            'except_channels' => [],
            'only_channels' => [],
            'formatter' => [
                'class' => ChannelMixedFormatter::class,
            ],
            'max_files' => get_env('LOG_CHANNEL_MAX_FILES', 30), // 最大文件数
            'name' => 'channelMixed', // 合并时的日志文件名
        ],
        // 控制台输出
        'stdout' => [
            'class' => WebmanTech\Logger\Mode\StdoutMode::class,
            'enable' => get_env('CHANNEL_LOG_MODE_STDOUT', false),
            'except_channels' => [],
            'only_channels' => [],
            'formatter' => [
                'class' => ChannelMixedFormatter::class,
            ],
        ],
        // 输出到 redis
        'redis' => [
            'class' => WebmanTech\Logger\Mode\RedisMode::class,
            'enable' => get_env('CHANNEL_LOG_MODE_REDIS', false),
            'except_channels' => [],
            'only_channels' => [],
            'formatter' => [
                'class' => ChannelFormatter::class,
            ],
            'redis' => function () {
                return \support\Redis::connection('default')->client();
            },
            'redis_key_prefix' => 'webmanLog:',
        ],
    ],
];
