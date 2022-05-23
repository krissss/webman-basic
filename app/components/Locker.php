<?php

namespace app\components;

use support\Container;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

/**
 * @internal 使用 support\facade\Locker
 */
class Locker
{
    public static function __callStatic($name, $arguments)
    {
        $key = $arguments[0] ?? '';
        $ttl = $arguments[1] ?? 300;
        $autoRelease = $arguments[2] ?? true;
        return static::createLock($name . $key, $ttl, $autoRelease);
    }

    /**
     * 创建锁
     * @param string $key
     * @param float|null $ttl
     * @param bool $autoRelease
     * @param string $prefix
     * @return LockInterface
     */
    protected static function createLock(string $key, ?float $ttl = 300, bool $autoRelease = true, string $prefix = 'lock_'): LockInterface
    {
        return static::getLockFactory()->createLock($prefix . $key, $ttl, $autoRelease);
    }

    protected static ?LockFactory $factory = null;

    /**
     * @return LockFactory
     */
    protected static function getLockFactory(): LockFactory
    {
        if (static::$factory === null) {
            $storage = config('lock.storage');
            $storageConfig = config('lock.storage_configs')[$storage];
            if (is_callable($storageConfig['construct'])) {
                $storageConfig['construct'] = call_user_func($storageConfig['construct']);
            }
            $storageInstance = Container::make($storageConfig['class'], $storageConfig['construct']);
            static::$factory = new LockFactory($storageInstance);
        }

        return static::$factory;
    }
}
