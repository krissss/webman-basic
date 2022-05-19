<?php

namespace app\components;

use app\components\interfaces\SecurityInterface;
use support\Container;

/**
 * @method static SecurityInterface security()
 */
class Component
{
    public static function dependence(): array
    {
        return [
            'security' => [
                'alias' => [SecurityInterface::class],
                'singleton' => Security::class,
            ],
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        return Container::get($name);
    }
}
