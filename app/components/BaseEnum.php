<?php

namespace app\components;

use ReflectionClass;
use ReflectionException;

abstract class BaseEnum
{
    /**
     * @var array
     */
    private static array $constCacheArray = [];

    /**
     * 获取全部的 const
     * @return mixed
     * @throws ReflectionException
     */
    public static function getConstants()
    {
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    /**
     * 获取选择的数组
     * @return array
     */
    public static function getViewItems(): array
    {
        $array = static::getConstants();
        $selectArray = [];
        foreach ($array as $key => $value) {
            $selectArray[$value] = static::getDefaultDescription($key);
        }
        return $selectArray;
    }

    /**
     * 获取全部键
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * 获取全部值
     * @return array
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * 获取某个值的描述
     * @param $value
     * @param string $unKnown
     * @return mixed|string
     */
    public static function getDescription($value, string $unKnown = 'Unknown')
    {
        $array = static::getViewItems();
        return $array[$value] ?? $unKnown;
    }

    /**
     * 获取默认的描述
     * @param string $key
     * @return string
     */
    protected static function getDefaultDescription(string $key): string
    {
        if (ctype_upper($key)) {
            $key = strtolower($key);
        }
        return ucwords(str_replace('_', ' ', $key));
    }
}
