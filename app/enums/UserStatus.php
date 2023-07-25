<?php

namespace app\enums;

use app\components\BaseEnum;
use app\enums\traits\EnumLabelSupport;

class UserStatus extends BaseEnum
{
    use EnumLabelSupport;

    public const ENABLE = 0; // 可用
    public const DISABLED = 10; // 禁用

    /**
     * {@inheritdoc}
     */
    public static function getViewItems(): array
    {
        return [
            self::ENABLE => '可用',
            self::DISABLED => '禁用',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getViewLabels(): array
    {
        return [
            self::ENABLE => 'success',
            self::DISABLED => 'danger',
        ];
    }
}
