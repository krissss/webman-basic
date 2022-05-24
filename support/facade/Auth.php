<?php

namespace support\facade;

use app\model\Admin;
use Kriss\WebmanAuth\Interfaces\GuardInterface;

class Auth extends \Kriss\WebmanAuth\facade\Auth
{
    public static function guardUser(): GuardInterface
    {
        return self::guard('user');
    }

    public static function guardAdmin(): GuardInterface
    {
        return self::guard('admin');
    }

    public static function identityAdmin(): Admin
    {
        return self::guardAdmin()->getUser();
    }
}
