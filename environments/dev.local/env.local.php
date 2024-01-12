<?php

// 动态切换 environments 下的环境，不需要每次都初始化 根目录下的 env.php，适用于 environments 下的 env.php 有全量配置的情况
$env = 'dev';
require_once base_path("environments/{$env}/env.php");

// 直接覆盖根目录下的 env.php，可以每次 init 来初始化 根目录下的 env.php，适用于使用 nacos 等配置注册中心的情况
//require_once __DIR__ . '/env.php';

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
put_env('NACOS_ENABLE', false);
put_env('NACOS_ENABLE_CONFIG_CENTER', false);
