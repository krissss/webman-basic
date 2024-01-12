<?php

$writablePath = [
    'runtime',
    //'public/assets',
];

$setExecutablePath = [
    'webman',
    'artisan',
];

$envArr = [
    'dev.local' => 'dev.local',
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
