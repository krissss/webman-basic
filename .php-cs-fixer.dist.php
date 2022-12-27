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
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        // ->exclude('folder-to-exclude') // if you want to exclude some folders, you can do it like this!
        ->in([
            __DIR__ . '/app',
        ])
    )
    ;
