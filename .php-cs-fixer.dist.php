<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13.1|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setLineEnding("\n")
    ->setCacheFile(__DIR__ . '/runtime/.php-cs-fixer.cache')
    ->setFinder(
        PhpCsFixer\Finder::create()
        ->in([
            __DIR__ . '/app',
            __DIR__ . '/support/facade',
        ])
        ->notPath([
            __DIR__ . '/app/command/TestCommand.php',
        ])
    )
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
        // 其他
        'no_unused_imports' => true, // 去除无用的 import
        'native_function_casing' => true, // php 函数使用正确的大小写
        'standardize_not_equals' => true, // <> => !=
        'cast_spaces' => ['space' => 'none'], // 强制类型转化无空格
        'concat_space' => ['spacing' => 'one'], // 连接字符串之间要一个空格
    ]);
