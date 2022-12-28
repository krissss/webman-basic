<?php

namespace support\facade;

use Symfony\Component\Lock\LockInterface;

/**
 * @deprecated 使用 CacheLocker
 *
 * @method static LockInterface test(?string $key = null, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null)
 */
class Locker extends \WebmanTech\SymfonyLock\Locker
{
}
