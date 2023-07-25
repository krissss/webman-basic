<?php

namespace app\middleware;

/**
 * 请求参数处理前后空格
 */
class TrimStings extends TransformRequest
{
    /**
     * @var array
     */
    protected $except = [
        /*'password',
        'password_confirmation',*/
    ];

    /**
     * {@inheritDoc}
     */
    protected function transform(string $key, $value)
    {
        if (\in_array($key, $this->except, true)) {
            return $value;
        }

        return \is_string($value) ? trim($value) : $value;
    }
}
