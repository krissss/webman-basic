<?php

namespace support\facade;

class CacheWebman extends \support\Cache
{
    public static function instance()
    {
        return \WebmanTech\LaravelCache\Facades\Cache::psr16();
    }
}
