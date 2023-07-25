<?php

namespace support\facade;

use Illuminate\Contracts\Cache\Lock;

/**
 * @see https://laravel.com/docs/8.x/cache#managing-locks
 *
 * @method static Lock test(?string $key = null, int $seconds = 0)
 * @method static Lock restoreTest(?string $key, string $owner)
 */
class CacheLocker
{
    public static function __callStatic($name, $arguments)
    {
        if (0 === strpos($name, 'restore')) {
            $name = lcfirst(substr($name, \strlen('restore')));
            $key = $arguments[0] ?? '';
            $owner = $arguments[1];

            return Cache::restoreLock(self::getLockName([$name, $key]), $owner);
        }

        $key = $arguments[0] ?? '';
        $seconds = (int) ($arguments[1] ?? 0);

        return Cache::lock(self::getLockName([$name, $key]), $seconds);
    }

    private static function getLockName(array $keys): string
    {
        return implode(':', $keys);
    }
}
