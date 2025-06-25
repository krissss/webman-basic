<?php

namespace app\enums\common;

use app\components\BaseEnum;
use app\enums\traits\EnumLabelSupport;

class OnOffStatus extends BaseEnum
{
    use EnumLabelSupport;

    public const ON = 1;
    public const OFF = 0;

    /**
     * {@inheritdoc}
     */
    public static function getViewItems(): array
    {
        return [
            self::ON => '启用',
            self::OFF => '禁用',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getViewLabels(): array
    {
        return [
            self::ON => 'success',
            self::OFF => 'danger',
        ];
    }
}
