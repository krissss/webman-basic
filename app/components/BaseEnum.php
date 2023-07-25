<?php

namespace app\components;

abstract class BaseEnum
{
    private static array $constCacheArray = [];

    /**
     * 获取全部的 const.
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public static function getConstants()
    {
        $calledClass = static::class;
        if (!\array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * 获取选择的数组.
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

    public static function getLabelValue(): array
    {
        $data = [];
        foreach (static::getViewItems() as $value => $label) {
            $data[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $data;
    }

    /**
     * 获取全部键.
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * 获取全部值
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * 获取某个值的描述.
     *
     * @return mixed|string
     */
    public static function getDescription($value, string $unKnown = 'Unknown')
    {
        $array = static::getViewItems();

        return $array[$value] ?? $unKnown;
    }

    /**
     * 获取默认的描述.
     */
    protected static function getDefaultDescription(string $key): string
    {
        if (ctype_upper($key)) {
            $key = strtolower($key);
        }

        return ucwords(str_replace('_', ' ', $key));
    }
}
