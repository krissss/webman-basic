<?php

namespace app\enums\common;

use app\enums\traits\ViewItemsTrait;

enum YesNoEnum: int
{
    use ViewItemsTrait;

    case Yes = 1;
    case No = 0;

    public function description(): string
    {
        return match ($this) {
            self::Yes => '是',
            self::No => '否',
        };
    }
}
