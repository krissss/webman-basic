<?php

namespace app\middleware;

/**
 * 请求参数空字符转 null.
 */
class ConvertEmptyStringsToNull extends TransformRequest
{
    /**
     * {@inheritDoc}
     */
    protected function transform(string $key, $value)
    {
        return '' === $value ? null : $value;
    }
}
