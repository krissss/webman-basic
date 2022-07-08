<?php

namespace app\enums;

use app\components\BaseEnum;

class AdminStatus extends BaseEnum
{
    const ENABLE = 0; // 可用
    const DISABLED = 10; // 禁用

    public static function getViewItems(): array
    {
        return [
            self::ENABLE => '可用',
            self::DISABLED => '禁用',
        ];
    }
}
