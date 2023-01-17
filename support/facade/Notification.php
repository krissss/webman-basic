<?php

namespace support\facade;

use Kriss\Notification\Channels;

/**
 * @method static Channels\WeWorkBotChannel weWorkBot()
 */
class Notification extends \Kriss\Notification\Integrations\Webman\Notification
{
    public const CHANNEL_DEFAULT = 'weWorkBot';

    public static function channels(): array
    {
        return [
            'weWorkBot' => [
                'class' => Channels\WeWorkBotChannel::class,
                'key' => '',
                'mentioned_list' => [],
                'rate_limit' => [
                    'key' => ['global weWorkBot', 'v1'],
                    'maxAttempts' => 2,
                    'decaySeconds' => 60,
                ],
            ],
        ];
    }
}
