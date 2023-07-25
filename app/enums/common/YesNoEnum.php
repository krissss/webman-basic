<?php

namespace app\enums\common;

use app\components\BaseEnum;

class YesNoEnum extends BaseEnum
{
    public const YES = 1;
    public const NO = 0;

    /**
     * {@inheritdoc}
     */
    public static function getViewItems(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }
}
