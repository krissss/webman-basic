<?php
declare(strict_types=1);

$processes = [];
if (get_env('NACOS_ENABLE_CONFIG_CENTER', true)) {
    $processes['config-listener'] = [
        # 多Timber异步监听器，多个监听异步非阻塞执行
        'handler' => \Workbunny\WebmanNacos\Process\AsyncConfigListenerProcess::class,
        // # 单Timer同步监听器，多个监听并发且阻塞执行
        //'handler'  => \Workbunny\WebmanNacos\Process\ConfigListenerProcess::class
    ];
}
if (get_env('NACOS_ENABLE_SERVICE_REGISTER', true)) {
    $processes['instance-registrar'] = [
        'handler' => \Workbunny\WebmanNacos\Process\InstanceRegistrarProcess::class
    ];
}

return $processes;
