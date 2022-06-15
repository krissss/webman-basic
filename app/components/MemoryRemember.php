<?php

namespace app\components;

use app\components\interfaces\MemoryRememberInterface;

class MemoryRemember implements MemoryRememberInterface
{
    protected array $data = [];

    /**
     * @inheritDoc
     */
    public function set($key, $value): void
    {
        $this->data[Tools::buildCacheKey($key)] = $value;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        return $this->data[Tools::buildCacheKey($key)] ?? $default;
    }
}
