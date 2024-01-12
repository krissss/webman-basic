<?php

// nacos 配置的例子

\app\command\framework\InitNacosConfigCommand::checkMustInit();

// region nacos
put_env('NACOS_ENABLE', false);
put_env('NACOS_ENABLE_CONFIG_CENTER', true); // 配置中心独立开关
put_env('NACOS_HOST', '127.0.0.1');
put_env('NACOS_PORT', 8848);
put_env('NACOS_NAMESPACE_ID', ''); // 命名空间，服务注册和配置中心共用
put_env('NACOS_GROUP', null); // 分组，不填，默认 DEFAULT_GROUP
put_env('NACOS_SERVICE_NAME', 'webman-basic'); // 服务名，默认为 config('app.name')
// endregion
