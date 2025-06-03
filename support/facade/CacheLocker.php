<?php

namespace support\facade;

use Illuminate\Contracts\Cache\Lock;

/**
 * @see https://laravel.com/docs/cache#managing-locks
 *
 * @method static Lock test(?string $key = null, int $seconds = 0)
 * @method static Lock restoreTest(?string $key, string $owner)
 */
class CacheLocker extends \WebmanTech\LaravelCache\Facades\CacheLocker
{
}
