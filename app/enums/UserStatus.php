<?php

namespace app\enums;

use app\enums\common\OnOffStatus;

class UserStatus extends OnOffStatus
{
    public const ENABLE = self::ON;
    public const DISABLE = self::OFF;
}
