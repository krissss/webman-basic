<?php

use Kriss\WebmanLogger\Formatter\ChannelFormatter;
use Kriss\WebmanLogger\Formatter\ChannelMixedFormatter;
use Kriss\WebmanLogger\Processors\RequestRouteProcessor;
use Kriss\WebmanLogger\Processors\RequestUidProcessor;
use Kriss\WebmanLogger\Processors\CurrentUserProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use support\facade\Auth;

return [
    // channels
    'channels' => [
        //'channelName',
        'sql',
        'app',
    ],
    // 记录等级，仅大于设定等级的日志才会真实写入日志文件
    'levels' => [
        // 默认等级
        'default' => config('app.debug') ? 'debug' : 'info',
        // 特殊的等级
        'special' => [
            //'channelName' => 'info',
        ],
    ],
    // processors
    'processors' => function () {
        return [
            new PsrLogMessageProcessor('Y-m-d H:i:s'),
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
            'class' => Kriss\WebmanLogger\Mode\SplitMode::class,
            'enable' => get_env('LOG_CHANNEL_MODE_SPLIT', true),
            'except_channels' => explode(',', get_env('CHANNEL_LOG_MODE_SPLIT_EXCEPT', '')),
            'formatter' => [
                'class' => ChannelFormatter::class,
            ],
            'max_files' => get_env('LOG_CHANNEL_MAX_FILES', 30), // 最大文件数
        ],
        // 将所有channel合并到一起记录
        'mix' => [
            'class' => Kriss\WebmanLogger\Mode\MixMode::class,
            'enable' => get_env('CHANNEL_LOG_MODE_MIX', false),
            'except_channels' => explode(',', get_env('CHANNEL_LOG_MODE_MIX_EXCEPT', '')),
            'formatter' => [
                'class' => ChannelMixedFormatter::class,
            ],
            'max_files' => get_env('LOG_CHANNEL_MAX_FILES', 30), // 最大文件数
            'name' => 'channelMixed', // 合并时的日志文件名
        ],
        // 控制台输出
        'stdout' => [
            'class' => Kriss\WebmanLogger\Mode\StdoutMode::class,
            'enable' => get_env('CHANNEL_LOG_MODE_STDOUT', false),
            'except_channels' => explode(',', get_env('CHANNEL_LOG_MODE_STDOUT_EXCEPT', '')),
            'formatter' => [
                'class' => ChannelMixedFormatter::class,
            ],
        ],
    ],
];
