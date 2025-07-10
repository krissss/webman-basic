<?php

namespace app\enums;

final class EnumCache
{
    private static array $cache = [];

    public static function getOrSetViewItems(string $classname, \Closure $fn)
    {
        if (!isset(self::$cache[$classname])) {
            self::$cache[$classname] = $fn();
        }

        return self::$cache[$classname];
    }
}
