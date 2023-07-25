<?php

namespace support\facade;

use app\model\Admin;
use app\model\User;
use WebmanTech\Auth\Interfaces\GuardInterface;

class Auth extends \WebmanTech\Auth\Auth
{
    public static function guardUser(): GuardInterface
    {
        return self::guard('user');
    }

    public static function identityUser(): User
    {
        /** @var User $model */
        $model = self::guardUser()->getUser();

        return $model;
    }

    public static function guardAdmin(): GuardInterface
    {
        return self::guard('admin');
    }

    public static function identityAdmin(): Admin
    {
        /** @var Admin $model */
        $model = self::guardAdmin()->getUser();

        return $model;
    }

    public static function guardApiUser(): GuardInterface
    {
        return self::guard('api_user');
    }

    public static function identityApiUser(): User
    {
        /** @var User $model */
        $model = self::guardApiUser()->getUser();

        return $model;
    }

    public static function getId(): string
    {
        return static::guard()->getId();
    }

    public static function getName(): string
    {
        return optional(static::guard()->getUser())->name ?? '';
    }
}
