<?php

use support\facade\Validator;

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
 * 验证其
 * @param array $data
 * @param array $rules
 * @param array $messages
 * @param array $customAttributes
 * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
 */
function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
{
    $factory = Validator::instance();

    if (func_num_args() === 0) {
        return $factory;
    }

    return $factory->make($data, $rules, $messages, $customAttributes);
}
