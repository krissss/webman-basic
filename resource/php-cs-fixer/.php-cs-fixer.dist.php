<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13.1|configurator
 * you can change this configuration by importing this file.
 */

$config = new PhpCsFixer\Config();
$allowRisky = strpos(implode(' ', $_SERVER['argv'] ?? []), '--allow-risky=yes') !== false;
$rootPath = __DIR__ . '/../..';
return $config
    ->setLineEnding(PHP_EOL)
    ->setCacheFile($rootPath . '/runtime/.php-cs-fixer.cache')
    ->setFinder(PhpCsFixer\Finder::create()
        ->in([
            $rootPath . '/app',
            $rootPath . '/config',
            $rootPath . '/resource',
            $rootPath . '/support',
        ])
        ->notPath([
            $rootPath . '/app/command/TestCommand.php',
        ])
    )
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => $allowRisky,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => $allowRisky,
        // @PSR12 的调整
        'function_declaration' => [
            'closure_fn_spacing' => 'none', // fn() 后不带空格
        ],
        // @PHP74Migration:risky 的调整
        'declare_strict_types' => false, // 不强制定义 declare(strict_types=1);
        'void_return' => false, // 不强制添加 void 返回类型
        'trailing_comma_in_multiline' => false, // 不强制数组末尾的逗号（建议，但由于太多不合规的，因此禁用）
        'visibility_required' => ['elements' => ['property', 'method', /*'const'*/]], // const 不强制加 public
        // 其他
        'no_unused_imports' => true, // 去除无用的 import
        'native_function_casing' => true, // php 函数使用正确的大小写
        'standardize_not_equals' => true, // <> => !=
        'cast_spaces' => ['space' => 'none'], // 强制类型转化无空格
        'concat_space' => ['spacing' => 'one'], // 连接字符串之间要一个空格
    ])
    ;
