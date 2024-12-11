<?php
/**
 * 该文件会在 composer autoload 之前载入，可以用于覆盖一些 composer 依赖中通过 files 加载的函数
 * @see \support\facade\Composer::postAutoloadDump()
 */

use Illuminate\Contracts\Container\Container as LaravelContainer;
use Webman\Route;

/**
 * 设置 env
 * @param string $key
 * @param mixed $value
 * @return void
 */
function put_env(string $key, $value)
{
    \app\components\EnvRepository::set($key, $value);
}

/**
 * 获取 env
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_env(string $key, $default = null)
{
    return \app\components\EnvRepository::get($key, $default);
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

/**
 * Create url
 * 覆盖 webman 自带的，支持 pathPrefix
 * @param string $name
 * @param ...$parameters
 * @return string
 */
function route(string $name, ...$parameters): string
{
    if ($parameters && \is_array(\current($parameters))) {
        $parameters = \current($parameters);
    }
    return route_url($name, $parameters);
}
