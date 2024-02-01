<?php

/** @var PhpCsFixer\Config $config */
$config = require __DIR__ . '/.php-cs-fixer.dist.php';

$config->setRules(array_merge(
    $config->getRules(),
    [
        // 切记不要修改与默认配置有冲突的
        //'trailing_comma_in_multiline' => true,
    ]
));

return $config;
