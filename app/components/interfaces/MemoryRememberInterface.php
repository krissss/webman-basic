<?php

namespace app\components\interfaces;

/**
 * 内存记录器，仅将数据保留在内存中，下次启动或重启后会丢失.
 */
interface MemoryRememberInterface
{
    /**
     * 记录.
     *
     * @param string|array $key
     * @param mixed        $value
     */
    public function set($key, $value): void;

    /**
     * 根据 key 获取值
     *
     * @param string|array $key
     * @param null         $default
     *
     * @return mixed
     */
    public function get($key, $default = null);
}
