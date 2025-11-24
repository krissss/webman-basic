<?php

namespace app\components;

use Illuminate\Validation\ValidationException;

class Tools
{
    /**
     * 构建缓存键.
     *
     * @param mixed $keys
     */
    public static function buildKey($keys): string
    {
        if (\is_string($keys) && \strlen($keys) <= 32) {
            return $keys;
        }

        return md5(serialize($keys));
    }

    /**
     * 构造并抛出 ValidationException.
     */
    public static function buildValidationException(array $errors): ValidationException
    {
        $validator = validator()->make([], []);
        foreach ($errors as $key => $value) {
            $validator->errors()->add($key, $value);
        }

        return new ValidationException($validator);
    }

    /**
     * 格式化 Bytes.
     *
     * @param string|int|null $size
     */
    public static function formatBytes($size, int $precision = 2): string
    {
        if ($size === 0 || $size === null) {
            return '0B';
        }

        $sign = $size < 0 ? '-' : '';
        $size = abs($size);

        $base = log($size) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return $sign . round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }
}
