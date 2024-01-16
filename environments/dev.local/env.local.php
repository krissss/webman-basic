<?php

$__env = 'dev'; // environments 下的目录名
$__envFiles = [
    __DIR__ . '/env.php',
    base_path("environments/{$__env}/env.php"),
];
foreach ($__envFiles as $envFile) {
    if (file_exists($envFile)) {
        require $envFile;
        break;
    }
}

// 修改覆盖部分配置

// app
put_env('APP_DEBUG', true);

// server
put_env('SERVER_LISTEN', 'http://0.0.0.0:8787');

// redis
put_env('REDIS_HOST', '127.0.0.1');
put_env('REDIS_PORT', '6379');
put_env('REDIS_PASSWORD', null);

// log
put_env('LOG_MODE_SPLIT', true);
put_env('LOG_MODE_MIX', false);
put_env('LOG_MODE_STDOUT', true);

// queue
put_env('QUEUE_ENABLE', false);

// crontab
put_env('CRONTAB_ENABLE', false);

// nacos
if (!\app\command\framework\InitNacosConfigCommand::isInCommand()) {
    put_env('NACOS_ENABLE', false);
    put_env('NACOS_ENABLE_CONFIG_CENTER', false);
}
