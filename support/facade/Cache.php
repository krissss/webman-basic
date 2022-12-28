<?php

namespace support\facade;

use InvalidArgumentException;
use support\Redis;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Cache extends \support\Cache
{
    private static array $_instances = [];

    public static function instance(): Psr16Cache
    {
        return static::driver(config('cache.default'));
    }

    public static function driver(string $name): Psr16Cache
    {
        if (isset(self::$_instances[$name])) {
            return self::$_instances[$name];
        }

        $config = config('cache.drivers.' . $name, []);
        $driver = $config['driver'];
        unset($config['driver']);
        if ($driver === 'file') {
            $config = array_merge([
                'default_ttl' => 0,
                'namespace' => 'webman',
                'save_path' => base_path() . '/runtime/cache',
            ], $config);
            $cache = new FilesystemAdapter($config['namespace'], $config['default_ttl'], $config['save_path']);
        } elseif ($driver === 'redis') {
            $config = array_merge([
                'default_ttl' => 0,
                'namespace' => 'webman',
                'connection' => 'cache',
            ], $config);
            $cache = new RedisAdapter(Redis::connection($config['connection'])->client(), $config['namespace'], $config['default_ttl']);
        } else {
            throw new InvalidArgumentException('redis.drivers 不支持: ' . $driver);
        }

        return self::$_instances[$name] = new Psr16Cache($cache);
    }
}
