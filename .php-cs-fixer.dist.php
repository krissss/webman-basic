<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13.1|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setLineEnding(PHP_EOL)
    ->setCacheFile(__DIR__ . '/runtime/.php-cs-fixer.cache')
    ->setRules([
        '@PSR12' => true,
        //'@PSR12:risky' => true,
        '@PHP74Migration' => true,
        //'@PHP74Migration:risky' => true,
        // @PSR12 的调整
        'function_declaration' => [
            'closure_fn_spacing' => 'none', // fn() 后不带空格
        ],
        // @PHP74Migration:risky 的调整
        'declare_strict_types' => false, // 不强制定义 declare(strict_types=1);
        'void_return' => false, // 不强制添加 void 返回类型
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        // ->exclude('folder-to-exclude') // if you want to exclude some folders, you can do it like this!
        ->in([
            __DIR__ . '/app',
        ])
    )
    ;
