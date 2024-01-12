<?php

// region app
put_env('APP_DEBUG', false);
put_env('APP_NAME', 'webman-basic');
// endregion

// region server
put_env('SERVER_LISTEN', 'http://0.0.0.0:8787');
// endregion

// region database
put_env('DB_MYSQL_HOST', '127.0.0.1');
put_env('DB_MYSQL_PORT', '3306');
put_env('DB_MYSQL_DATABASE', 'webman_basic');
put_env('DB_MYSQL_USERNAME', 'root');
put_env('DB_MYSQL_PASSWORD', 'root');
// endregion

// region redis
put_env('REDIS_HOST', '127.0.0.1');
put_env('REDIS_PORT', '6379');
put_env('REDIS_PASSWORD', null);
// endregion

// region session
put_env('SESSION_ADAPTER', 'redis'); // file/redis
// endregion

// region cache
put_env('CACHE_ADAPTER', 'redis'); // file/redis
// endregion

// region log
put_env('LOG_MODE_SPLIT', true);
put_env('LOG_MODE_MIX', false);
put_env('LOG_MODE_STDOUT', false);
put_env('LOG_MAX_FILES', 30);
// endregion

// region queue
put_env('QUEUE_ENABLE', false);
put_env('QUEUE_CONSUMER_ENABLE', true); // 消费者是否启用
put_env('QUEUE_REDIS_CONSUMER_COUNT', null); // queue 消费进程数
// endregion

// region crontab
put_env('CRONTAB_ENABLE', false);
// endregion

// region nacos
put_env('NACOS_ENABLE', false);
put_env('NACOS_ENABLE_CONFIG_CENTER', true); // 配置中心独立开关
put_env('NACOS_ENABLE_SERVICE_REGISTER', true); // 注册中心独立开关
put_env('NACOS_HOST', '127.0.0.1');
put_env('NACOS_PORT', 8848);
put_env('NACOS_NAMESPACE_ID', ''); // 命名空间，服务注册和配置中心共用
put_env('NACOS_GROUP', null); // 分组，不填，默认 DEFAULT_GROUP
put_env('NACOS_SERVICE_NAME', null); // 服务名，默认为 config('app.name')
put_env('NACOS_SERVICE_IP', null); // 服务注册 ip，不填，默认取本机 ip
put_env('NACOS_SERVICE_PORT', null); // 服务注册端口，不填，默认取当前服务端口
put_env('NACOS_SERVICE_EXTRA_NAMESPACES', null); // 实例额外注册的 namespaceId，多个用逗号分开
// endregion
