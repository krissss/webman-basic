<?php

namespace support\facade;

use Illuminate\Contracts\Translation\Loader as LoaderContract;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Laravel translation
 *
 * @method static string get($key, array $replace = [], $locale = null)
 * @method static string choice($key, $number, array $replace = [], $locale = null)
 * @method static string getLocale()
 * @method static void setLocale(string $locale)
 */
class TranslationLaravel
{
    protected static ?TranslatorContract $_instance = null;

    public static function instance(): TranslatorContract
    {
        if (!static::$_instance) {
            static::$_instance = static::createTranslation();
        }
        return static::$_instance;
    }

    protected static function createTranslation(): TranslatorContract
    {
        $loader = static::createLoader();
        $translator = new Translator($loader, config('translation.locale', 'zh_CN'));
        $fallback = config('translation.fallback_locale', []);
        if ($fallback) {
            if (is_array($fallback)) {
                $fallback = $fallback[0];
            }
            $translator->setFallback($fallback);
        }
        return $translator;
    }

    protected static function createLoader(): LoaderContract
    {
        return new FileLoader(new Filesystem(), config('translation.path'));
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
