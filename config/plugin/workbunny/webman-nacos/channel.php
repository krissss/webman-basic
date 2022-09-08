<?php
return [
    'default' => [
        'host' => get_env('NACOS_HOST', '127.0.0.1'),
        'port' => (int)get_env('NACOS_PORT', 8848),
        'username' => null,
        'password' => null,
    ],
];
