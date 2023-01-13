<?php

namespace support\facade;

use Kriss\Notification\Channels;

/**
 * @method static Channels\WeWorkBotChannel default()
 */
class Notification extends \Kriss\Notification\Integrations\Webman\Notification
{
    public const CHANNEL_DEFAULT = 'default';

    public static function channels(): array
    {
        return [
            'default' => [
                'class' => Channels\WeWorkBotChannel::class,
                'key' => '',
                'mentioned_list' => [],
            ],
        ];
    }
}
