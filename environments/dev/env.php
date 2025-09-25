<?php

// region app
put_env('APP_DEBUG', true);
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
put_env('SESSION_ADAPTER', 'file'); // file/redis
// endregion

// region cache
put_env('CACHE_ADAPTER', 'file'); // file/redis
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
