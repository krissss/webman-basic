<?php

namespace app\enums;

use app\enums\traits\ViewItemsTrait;

enum AdminStatusEnum: int
{
    use ViewItemsTrait;

    case Enabled = 0;
    case Disabled = 10;

    public function description(): string
    {
        return match ($this) {
            self::Enabled => '启用',
            self::Disabled => '禁用',
        };
    }

    public function viewLabel(): string
    {
        return match ($this) {
            self::Enabled => 'success',
            self::Disabled => 'danger',
        };
    }

    public static function isEnabled(int $status): bool
    {
        return $status === self::Enabled->value;
    }
}
