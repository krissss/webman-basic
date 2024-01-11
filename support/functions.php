<?php

use Illuminate\Contracts\Container\Container as LaravelContainer;

use Webman\Route;

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
 * 获取容器实例
 * @return LaravelContainer
 */
function container(): LaravelContainer
{
    return \support\Container::instance();
}

/**
 * get from container
 * @param string $name
 * @return mixed|string
 */
function container_get(string $name)
{
    return \support\facade\Container::get($name);
}

/**
 * make from container
 * @param string $name
 * @param array $parameters
 * @return mixed|string
 */
function container_make(string $name, array $parameters = [])
{
    return \support\facade\Container::make($name, $parameters);
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

/**
 * 获取 public 下的 url 地址
 * @param string $path
 * @return string
 */
function public_url(string $path): string
{
    return \request()->pathPrefix() . $path;
}

/**
 * 获取路由地址
 * @param string $nameOrPath 路由 name 或 path
 * @param array $params
 * @return string
 */
function route_url(string $nameOrPath, array $params = []): string
{
    $route = Route::getByName($nameOrPath);
    if (!$route) {
        $route = new Route\Route([], $nameOrPath, function () {
        });
    }

    return \request()->pathPrefix() . $route->url($params);
}
