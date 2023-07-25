<?php

namespace support\facade;

/**
 * 请尽量从 $request 中获取 session.
 *
 * @method static string     getId()
 * @method static mixed|null get($name, $default = null)
 * @method static void       set($name, $value)
 * @method static void       delete($name)
 * @method static mixed|null pull($name, $default = null)
 * @method static void       put($key, $value = null)
 * @method static void       forget($name)
 * @method static array      all()
 * @method static void       flush()
 * @method static bool       has($name)
 * @method static bool       exists($name)
 * @method static void       save()
 * @method static bool       refresh()
 * @method static void       gc()
 */
class Session
{
    public static function instance(): \Workerman\Protocols\Http\Session
    {
        return request()->session();
    }

    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(...$arguments);
    }
}
