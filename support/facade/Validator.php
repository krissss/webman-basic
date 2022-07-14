<?php

namespace support\facade;

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\Factory;

/**
 * @method static ValidatorContract make(array $data, array $rules, array $messages = [], array $customAttributes = [])
 * @method static void extend($rule, $extension, $message = null)
 * @method static void extendImplicit($rule, $extension, $message = null)
 * @method static void replacer($rule, $replacer)
 */
class Validator
{
    protected static ?FactoryContract $_instance = null;

    public static function instance(): FactoryContract
    {
        if (!static::$_instance) {
            static::$_instance = static::createFactory();
        }
        return static::$_instance;
    }

    protected static function createFactory(): FactoryContract
    {
        return new Factory(TranslationLaravel::instance());
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}
