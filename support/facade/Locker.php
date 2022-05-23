<?php

namespace support\facade;

use Symfony\Component\Lock\LockInterface;

/**
 * @method static LockInterface test($key = null, $ttl = 300, $autoRelease = true)
 */
class Locker extends \app\components\Locker
{
}
