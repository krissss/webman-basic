<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 *
 * @see      http://www.workerman.net/
 *
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\Container;
use support\Request;
use support\Response;
use support\Translation;
use support\view\Blade;
use support\view\Raw;
use support\view\ThinkPHP;
use support\view\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Webman\App;
use Webman\Config;
use Webman\Route;
use Workerman\Protocols\Http\Session;
use Workerman\Worker;

// Project base path
define('BASE_PATH', dirname(__DIR__));

/**
 * return the program execute directory.
 */
function run_path(string $path = ''): string
{
    static $runPath = '';
    if (!$runPath) {
        $runPath = is_phar() ? dirname(Phar::running(false)) : BASE_PATH;
    }

    return path_combine($runPath, $path);
}

/**
 * if the param $path equal false,will return this program current execute directory.
 *
 * @param string|false $path
 */
function base_path($path = ''): string
{
    if (false === $path) {
        return run_path();
    }

    return path_combine(BASE_PATH, $path);
}

/**
 * App path.
 */
function app_path(string $path = ''): string
{
    return path_combine(BASE_PATH.\DIRECTORY_SEPARATOR.'app', $path);
}

/**
 * Public path.
 */
function public_path(string $path = ''): string
{
    static $publicPath = '';
    if (!$publicPath) {
        $publicPath = config('app.public_path') ?: run_path('public');
    }

    return path_combine($publicPath, $path);
}

/**
 * Config path.
 */
function config_path(string $path = ''): string
{
    return path_combine(BASE_PATH.\DIRECTORY_SEPARATOR.'config', $path);
}

/**
 * Runtime path.
 */
function runtime_path(string $path = ''): string
{
    static $runtimePath = '';
    if (!$runtimePath) {
        $runtimePath = config('app.runtime_path') ?: run_path('runtime');
    }

    return path_combine($runtimePath, $path);
}

/**
 * Generate paths based on given information.
 */
function path_combine(string $front, string $back): string
{
    return $front.($back ? (\DIRECTORY_SEPARATOR.ltrim($back, \DIRECTORY_SEPARATOR)) : $back);
}

/**
 * Response.
 */
function response(string $body = '', int $status = 200, array $headers = []): Response
{
    return new Response($status, $headers, $body);
}

/**
 * Json response.
 */
function json($data, int $options = \JSON_UNESCAPED_UNICODE): Response
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}

/**
 * Xml response.
 */
function xml($xml): Response
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }

    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}

/**
 * Jsonp response.
 */
function jsonp($data, string $callbackName = 'callback'): Response
{
    if (!is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }

    return new Response(200, [], "$callbackName($data)");
}

/**
 * Redirect response.
 */
function redirect(string $location, int $status = 302, array $headers = []): Response
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }

    return $response;
}

/**
 * View response.
 */
function view(string $template, array $vars = [], string $app = null, string $plugin = null): Response
{
    $request = request();
    $plugin = null === $plugin ? ($request->plugin ?? '') : $plugin;
    $handler = config($plugin ? "plugin.$plugin.view.handler" : 'view.handler');

    return new Response(200, [], $handler::render($template, $vars, $app, $plugin));
}

/**
 * Raw view response.
 *
 * @throws Throwable
 */
function raw_view(string $template, array $vars = [], string $app = null): Response
{
    return new Response(200, [], Raw::render($template, $vars, $app));
}

/**
 * Blade view response.
 */
function blade_view(string $template, array $vars = [], string $app = null): Response
{
    return new Response(200, [], Blade::render($template, $vars, $app));
}

/**
 * Think view response.
 */
function think_view(string $template, array $vars = [], string $app = null): Response
{
    return new Response(200, [], ThinkPHP::render($template, $vars, $app));
}

/**
 * Twig view response.
 *
 * @throws LoaderError
 * @throws RuntimeError
 * @throws SyntaxError
 */
function twig_view(string $template, array $vars = [], string $app = null): Response
{
    return new Response(200, [], Twig::render($template, $vars, $app));
}

/**
 * Get request.
 *
 * @return \Webman\Http\Request|Request|null
 */
function request()
{
    return App::request();
}

/**
 * Get config.
 *
 * @return array|mixed|null
 */
function config(string $key = null, $default = null)
{
    return Config::get($key, $default);
}

/**
 * Create url.
 */
function route(string $name, ...$parameters): string
{
    $route = Route::getByName($name);
    if (!$route) {
        return '';
    }

    if (!$parameters) {
        return $route->url();
    }

    if (is_array(current($parameters))) {
        $parameters = current($parameters);
    }

    return $route->url($parameters);
}

/**
 * Session.
 *
 * @return mixed|bool|Session
 */
function session($key = null, $default = null)
{
    $session = request()->session();
    if (null === $key) {
        return $session;
    }
    if (is_array($key)) {
        $session->put($key);

        return null;
    }
    if (strpos($key, '.')) {
        $keyArray = explode('.', $key);
        $value = $session->all();
        foreach ($keyArray as $index) {
            if (!isset($value[$index])) {
                return $default;
            }
            $value = $value[$index];
        }

        return $value;
    }

    return $session->get($key, $default);
}

/**
 * Translation.
 */
function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
{
    $res = Translation::trans($id, $parameters, $domain, $locale);

    return '' === $res ? $id : $res;
}

/**
 * Locale.
 */
function locale(string $locale = null): string
{
    if (!$locale) {
        return Translation::getLocale();
    }
    Translation::setLocale($locale);

    return $locale;
}

/**
 * 404 not found.
 */
function not_found(): Response
{
    return new Response(404, [], file_get_contents(public_path().'/404.html'));
}

/**
 * Copy dir.
 *
 * @return void
 */
function copy_dir(string $source, string $dest, bool $overwrite = false)
{
    if (is_dir($source)) {
        if (!is_dir($dest)) {
            mkdir($dest);
        }
        $files = scandir($source);
        foreach ($files as $file) {
            if ('.' !== $file && '..' !== $file) {
                copy_dir("$source/$file", "$dest/$file");
            }
        }
    } elseif (file_exists($source) && ($overwrite || !file_exists($dest))) {
        copy($source, $dest);
    }
}

/**
 * Remove dir.
 */
function remove_dir(string $dir): bool
{
    if (is_link($dir) || is_file($dir)) {
        return unlink($dir);
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file") && !is_link($dir)) ? remove_dir("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}

/**
 * Bind worker.
 */
function worker_bind($worker, $class)
{
    $callbackMap = [
        'onConnect',
        'onMessage',
        'onClose',
        'onError',
        'onBufferFull',
        'onBufferDrain',
        'onWorkerStop',
        'onWebSocketConnect',
        'onWorkerReload',
    ];
    foreach ($callbackMap as $name) {
        if (method_exists($class, $name)) {
            $worker->$name = [$class, $name];
        }
    }
    if (method_exists($class, 'onWorkerStart')) {
        call_user_func([$class, 'onWorkerStart'], $worker);
    }
}

/**
 * Start worker.
 *
 * @return void
 */
function worker_start($processName, $config)
{
    $worker = new Worker($config['listen'] ?? null, $config['context'] ?? []);
    $propertyMap = [
        'count',
        'user',
        'group',
        'reloadable',
        'reusePort',
        'transport',
        'protocol',
    ];
    $worker->name = $processName;
    foreach ($propertyMap as $property) {
        if (isset($config[$property])) {
            $worker->$property = $config[$property];
        }
    }

    $worker->onWorkerStart = function ($worker) use ($config) {
        require_once base_path('/support/bootstrap.php');
        if (isset($config['handler'])) {
            if (!class_exists($config['handler'])) {
                echo "process error: class {$config['handler']} not exists\r\n";

                return;
            }

            $instance = Container::make($config['handler'], $config['constructor'] ?? []);
            worker_bind($worker, $instance);
        }
    };
}

/**
 * Get realpath.
 */
function get_realpath(string $filePath): string
{
    if (str_starts_with($filePath, 'phar://')) {
        return $filePath;
    } else {
        return realpath($filePath);
    }
}

/**
 * Is phar.
 */
function is_phar(): bool
{
    return class_exists(Phar::class, false) && Phar::running();
}

/**
 * Get cpu count.
 */
function cpu_count(): int
{
    // Windows does not support the number of processes setting.
    if (\DIRECTORY_SEPARATOR === '\\') {
        return 1;
    }
    $count = 4;
    if (is_callable('shell_exec')) {
        if ('darwin' === strtolower(\PHP_OS)) {
            $count = (int) shell_exec('sysctl -n machdep.cpu.core_count');
        } else {
            $count = (int) shell_exec('nproc');
        }
    }

    return $count > 0 ? $count : 4;
}
