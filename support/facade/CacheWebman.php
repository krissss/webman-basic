<?php

namespace support\facade;

/**
 * @deprecated 使用 Cache
 */
class CacheWebman extends \support\Cache
{
    public static function instance()
    {
        return \WebmanTech\LaravelCache\Facades\Cache::psr16();
    }
}
