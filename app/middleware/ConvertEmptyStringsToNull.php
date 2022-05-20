<?php

namespace app\middleware;

class ConvertEmptyStringsToNull extends TransformRequest
{
    /**
     * @inheritDoc
     */
    protected function transform(string $key, $value)
    {
        return $value === '' ? null : $value;
    }
}
