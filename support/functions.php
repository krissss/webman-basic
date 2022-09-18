<?php

/**
 * 获取 .env 的配置
 * @param string $key
 * @param mixed $defaultValue
 * @param array $whichIsNull
 * @return mixed|null
 */
function get_env(string $key, $defaultValue = null, array $whichIsNull = ['', null, 'null', false])
{
    $value = getenv($key);
    if (in_array($value, $whichIsNull, true)) {
        if ($defaultValue instanceof Closure) {
            $defaultValue = call_user_func($defaultValue);
        }
        return $defaultValue;
    }
    return $value;
}

/**
 * 触发事件
 * @param string $eventName
 * @param mixed $data
 * @return int
 */
function event(string $eventName, $data = null): int
{
    return \support\facade\Event::emit($eventName, $data);
}
