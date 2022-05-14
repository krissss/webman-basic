<?php

$writablePath = [
    'runtime',
    //'public/assets',
];

$setExecutablePath = [
    'webman',
];

$envArr = [
    'dev' => 'dev',
    'prod' => 'prod',
];

$config = [];
foreach ($envArr as $name => $path) {
    $config[$name] = [
        'path' => $path,
        'setWritable' => $writablePath,
        'setExecutable' => $setExecutablePath,
    ];
}

return $config;
