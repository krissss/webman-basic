<?php

namespace support\facade;

use Symfony\Component\Lock\LockInterface;

/**
 * @method static LockInterface test(?string $key = null, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null)
 */
class Locker extends \Kriss\WebmanLock\Locker
{
}
