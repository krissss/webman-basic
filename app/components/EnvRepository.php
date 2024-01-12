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
    private static bool $supportSysEnv = true; // 是否要支持获取系统环境上的 env，为 true 时系统环境值优先

    /**
     * 设置 env
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        if (self::$readOnly) {
            throw new \InvalidArgumentException('readonly for envs');
        }
        self::$envs[$key] = $value;
    }

    /**
     * 获取 env
     * @param string $key
     * @param mixed $default
     * @param array $whichIsNull
     * @return mixed|null
     */
    public static function get(string $key, $default = null, array $whichIsNull = [null, ''])
    {
        if (!self::$defaultEnvLoaded) {
            self::loadDefaultEnvs();
        }
        $value = null;
        if (self::$supportSysEnv) {
            $value = getenv($key);
            if ($value === false) {
                $value = null;
            }
        }
        if ($value === null) {
            $value = self::$envs[$key] ?? null;
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
     * 将某些文件加载到 env，文件中使用 put_env 写入环境变量
     * @param array $phpFiles
     * @return void
     */
    public static function load(array $phpFiles): void
    {
        foreach ($phpFiles as $phpFile) {
            if (!file_exists($phpFile)) {
                continue;
            }
            require_once $phpFile;
        }
    }

    /**
     * 修改是否只读，只读下不允许再修改 env
     * @param bool $is
     * @return void
     */
    public static function changeReadOnly(bool $is): void
    {
        self::$readOnly = $is;
    }

    /**
     * 修改是否支持获取系统级的 env，为 true 时系统环境值优先
     * @param bool $support
     * @return void
     */
    public static function changeSupportSysEnv(bool $support): void
    {
        self::$supportSysEnv = $support;
    }

    /**
     * 加载默认的 env 文件
     * @return void
     */
    public static function loadDefaultEnvs(): void
    {
        $phpFiles = [
            base_path('/env.php'),
            base_path('/env.local.php'),
        ];
        self::load($phpFiles);

        self::$defaultEnvLoaded = true;
        self::changeReadOnly(true);
    }
}
