<?php

namespace app\enums\common;

use app\components\BaseEnum;

class YesNoEnum extends BaseEnum
{
    const YES = 1;
    const NO = 0;

    public static function getViewItems(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }
}
