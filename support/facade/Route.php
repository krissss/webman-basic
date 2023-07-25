<?php

namespace support\facade;

class Route extends \Webman\Route
{
    /**
     * 扩展原资源性路由，解决部分需求问题.
     *
     * name 问题 @see https://github.com/walkor/webman-framework/issues/46
     * 扩展自定义额外的 action（保留原来的 options 扩展）
     *
     * 使用方式
     * Route::resource('admin', AdminController::class, ['name_prefix' => 'admin.', 'resetPassword']);
     *
     * {@inheritDoc}
     */
    public static function resource(string $name, string $controller, array $options = [])
    {
        $options = array_merge([
            'name_prefix' => '',
        ], $options);

        $name = trim($name, '/');
        $namePrefixed = $options['name_prefix'].$name;

        if (method_exists($controller, 'index')) {
            static::get("/{$name}", [$controller, 'index'])->name("{$namePrefixed}.index");
        }
        if (method_exists($controller, 'create')) {
            static::get("/{$name}/create", [$controller, 'create'])->name("{$namePrefixed}.create");
        }
        if (method_exists($controller, 'store')) {
            static::post("/{$name}", [$controller, 'store'])->name("{$namePrefixed}.store");
        }
        if (method_exists($controller, 'update')) {
            static::put("/{$name}/{id}", [$controller, 'update'])->name("{$namePrefixed}.update");
        }
        if (method_exists($controller, 'show')) {
            static::get("/{$name}/{id}", [$controller, 'show'])->name("{$namePrefixed}.show");
        }
        if (method_exists($controller, 'edit')) {
            static::get("/{$name}/{id}/edit", [$controller, 'edit'])->name("{$namePrefixed}.edit");
        }
        if (method_exists($controller, 'destroy')) {
            static::delete("/{$name}/{id}", [$controller, 'destroy'])->name("{$namePrefixed}.destroy");
        }
        if (method_exists($controller, 'recovery')) {
            static::put("/{$name}/{id}/recovery", [$controller, 'recovery'])->name("{$namePrefixed}.recovery");
        }

        unset($options['name_prefix']);
        foreach ($options as $action => $config) {
            if (\is_int($action)) {
                $action = $config;
                $config = [];
            }
            if (!method_exists($controller, $action)) {
                continue;
            }
            $config = array_merge([
                'method' => 'post',
                'path' => '/{_name}/{_action}/{id}', // {_name} 和 {_action} 将被替换
            ], $config);
            $config['path'] = strtr($config['path'], [
                '{_name}' => $name,
                '{_action}' => $action,
            ]);
            $config['method'] = strtolower($config['method']);

            static::{$config['method']}($config['path'], [$controller, $action])->name("{$namePrefixed}.{$action}");
        }
    }
}
