<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13.1|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
$allowRisky = true;

return $config
    ->setLineEnding("\n")
    ->setCacheFile(__DIR__.'/runtime/.php-cs-fixer.cache')
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__.'/app',
                __DIR__.'/support/facade',
            ])
            ->notPath([
                __DIR__.'/app/command/TestCommand.php',
            ])
    )
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => $allowRisky,
        '@PHP74Migration' => true,
    ])
    ->setRiskyAllowed($allowRisky);
