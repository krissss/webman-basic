<?php

namespace app\components;

/**
 * env 环境的获取与加载
 */
final class EnvRepository
{
    private static array $envs = [];

    private static bool $readOnly = false;
    private static bool $defaultEnvLoaded = false;

    private static bool $supportReadonly = true; // 是否在读取后设为只读，防止 env 设置后再更新
    private static bool $supportSysEnv = true; // 是否要支持获取系统环境上的 env，为 true 时系统环境值优先
    private static bool $supportDefine = false; // 支持 define 的常量

    /**
     * 重置
     * @return void
     */
    final public static function reset(): void
    {
        self::$envs = [];

        self::$readOnly = false;
        self::$defaultEnvLoaded = false;

        self::$supportReadonly = true;
        self::$supportSysEnv = true;
        self::$supportDefine = false;
    }

    /**
     * 设置 env
     * @param string $key
     * @param mixed $value
     * @return void
     */
    final public static function set(string $key, mixed $value): void
    {
        if (self::$supportReadonly && self::$readOnly) {
            throw new \InvalidArgumentException('readonly for envs');
        }
        self::$envs[$key] = $value;
    }

    /**
     * 获取 env
     * @param string $key
     * @param mixed|null $default
     * @param array $whichIsNull
     * @return mixed|null
     */
    final public static function get(string $key, mixed $default = null, array $whichIsNull = [null, '']): mixed
    {
        if (!self::$defaultEnvLoaded) {
            self::loadDefaultEnvs();
        }
        $value = null;
        if (self::$supportSysEnv) {
            $value = $_SERVER[$key] ?? null;
        }
        if ($value === null) {
            $value = self::$envs[$key] ?? null;
        }
        if ($value === null && self::$supportDefine) {
            $value = defined($key) ? constant($key) : null;
        }
        if (in_array($value, $whichIsNull, true)) {
            $value = $default;
            if ($value instanceof \Closure) {
                $value = $value();
            }
        }
        return $value;
    }

    /**
     * 修改是否只读，只读下不允许再修改 env
     * @param bool $is
     * @return void
     */
    final public static function changeReadOnly(bool $is): void
    {
        self::$readOnly = $is;
    }

    /**
     * 修改是否支持获取系统级的 env，为 true 时系统环境值优先
     * @param bool $support
     * @return void
     */
    final public static function changeSupportReadonly(bool $support): void
    {
        self::$supportReadonly = $support;
    }

    /**
     * 修改是否支持获取系统级的 env，为 true 时系统环境值优先
     * @param bool $support
     * @return void
     */
    final public static function changeSupportSysEnv(bool $support): void
    {
        self::$supportSysEnv = $support;
    }

    /**
     * 修改是否支持 define
     * @param bool $support
     * @return void
     */
    final public static function changeSupportDefine(bool $support): void
    {
        self::$supportDefine = $support;
    }

    /**
     * 加载默认的 env 文件
     */
    final public static function loadDefaultEnvs(bool $force = false): void
    {
        // 已加载过的不再重复加载
        if (!$force && self::$defaultEnvLoaded) {
            return;
        }

        // 先标记为已加载，防止 env.php 中有使用到 get_env 时 导致死循环
        self::setDefaultEnvLoaded();

        $phpFiles = [
            'env.local.php',
            'env.php',
        ];
        foreach ($phpFiles as $phpFile) {
            $phpFile = base_path($phpFile);
            if (file_exists($phpFile)) {
                require $phpFile;
                // 仅加载一个，env.local.php 中可以主动去 require env.php
                break;
            }
        }

        if (self::$supportReadonly) {
            self::changeReadOnly(true);
        }
    }

    /**
     * 设置默认 env 是否被加载过
     */
    final public static function setDefaultEnvLoaded(bool $is = true): void
    {
        self::$defaultEnvLoaded = $is;
    }

    /**
     * 是否加载了默认的 env 文件
     * @return bool
     */
    final public static function isDefaultEnvLoaded(): bool
    {
        return self::$defaultEnvLoaded;
    }

    /**
     * 转为 define
     * @param array $config
     * @return void
     */
    final public static function transToDefine(array $config = []): void
    {
        if (self::$supportDefine) {
            $config = array_merge([
                'skip_defined' => true, // 已定义的忽略
                'exclude' => [], // 排除某些 key
            ], $config);

            foreach (self::$envs as $key => $value) {
                if (in_array($key, $config['exclude'])) {
                    continue;
                }
                if ($config['skip_defined'] && defined($key)) {
                    continue;
                }
                define($key, $value);
            }
        }
    }
}
