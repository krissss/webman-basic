<?php

namespace app\components;

use app\components\interfaces\MemoryRememberInterface;

class MemoryRemember implements MemoryRememberInterface
{
    protected array $data = [];

    /**
     * {@inheritDoc}
     */
    public function set($key, $value): void
    {
        $this->data[Tools::buildKey($key)] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        return $this->data[Tools::buildKey($key)] ?? $default;
    }
}
