<?php

namespace app\components;

use app\components\interfaces\MemoryRememberInterface;
use app\components\interfaces\SecurityInterface;
use support\facade\Container;

/**
 * @method static SecurityInterface       security()
 * @method static MemoryRememberInterface memoryRemember()
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
            'memoryRemember' => [
                'alias' => [MemoryRememberInterface::class],
                'singleton' => MemoryRemember::class,
            ],
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        return Container::get($name);
    }
}
