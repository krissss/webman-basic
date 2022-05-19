<?php

namespace support\facade;

use support\Redis;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Cache extends \support\Cache
{
    public static function instance()
    {
        if (!static::$_instance) {
            $adapter = config('cache.default');
            if ($adapter === 'file') {
                $config = array_merge([
                    'default_ttl' => 0,
                    'namespace' => 'webman',
                    'save_path' => base_path() . '/runtime/cache',
                ], config('cache.file', []));
                $cache = new FilesystemAdapter($config['namespace'], $config['default_ttl'], $config['save_path']);
            } elseif ($adapter === 'redis') {
                $config = array_merge([
                    'default_ttl' => 0,
                    'namespace' => 'webman',
                    'connection' => 'cache',
                ], config('cache.redis', []));
                $cache = new RedisAdapter(Redis::connection($config['connection'])->client(), $config['namespace'], $config['default_ttl']);
            } else {
                throw new \InvalidArgumentException('redis.default 不支持: ' . $adapter);
            }
            self::$_instance = new Psr16Cache($cache);
        }
        return static::$_instance;
    }
}
