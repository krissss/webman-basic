<?php

namespace app\components;

use app\components\interfaces\MemoryRememberInterface;
use app\components\interfaces\SecurityInterface;

/**
 * @method static SecurityInterface security()
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
        return container_get($name);
    }
}
