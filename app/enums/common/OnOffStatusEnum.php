<?php

namespace app\enums\common;

use app\enums\traits\ViewItemsTrait;

enum OnOffStatusEnum: int
{
    use ViewItemsTrait;

    case On = 1;
    case Off = 0;

    public function description(): string
    {
        return match ($this) {
            self::On => '启用',
            self::Off => '禁用',
        };
    }

    public function viewLabel(): string
    {
        return match ($this) {
            self::On => 'success',
            self::Off => 'danger',
        };
    }
}
