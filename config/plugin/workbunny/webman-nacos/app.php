<?php

use app\components\Tools;

if (!get_env('NACOS_ENABLE', false)) {
    return [];
}

$namespaceId = get_env('NACOS_NAMESPACE_ID', '');
$groupName = get_env('NACOS_GROUP', 'DEFAULT_GROUP');

$configListeners = collect([
    // 在此处增加配置中心的配置
    // $dateId => $filePath
    // 'webman-env' => base_path() . '.env',
])->map(
    fn($filepath, $dataId) => [
        $dataId,
        $groupName,
        $namespaceId,
        $filepath,
    ]
)->toArray();

return [
    'enable' => true,

    'host' => get_env('NACOS_HOST', '127.0.0.1'),
    'port' => (int)get_env('NACOS_PORT', 8848),
    /* username 和 password 同时设置为 null，不启用认证 */
    'username' => null,
    'password' => null,

    /** 长轮询等待时长 毫秒 @desc 当长轮询间隔不存在时，该项作为默认值使用，其余时间则不生效 */
    'long_pulling_timeout' => 30000,

    /** 长轮询间隔 秒 @desc 组件包主要使用该项作为监听器间隔，使用{该值 * 1000}作为长轮询等待时长 */
    'long_pulling_interval' => 30,

    /**
     * 配置文件监听器
     * @desc 可在config/plugin/workbunny/webman-nacos/process.php中进行修改以下两种监听器
     * @see \Workbunny\WebmanNacos\Process\ConfigListenerProcess 单Timer同步监听器
     * @see \Workbunny\WebmanNacos\Process\AsyncConfigListenerProcess 多Timer异步监听器
     */
    'config_listeners' => $configListeners,

    /**
     * 实例注册器
     * @see \Workbunny\WebmanNacos\Process\InstanceRegistrarProcess
     * @desc 这里的实例注册器主要用于静态的已知的实例注册，如果是项目内动态的实例注册，可以结合AOP+注解去实现对某个服务或者某个控制器的注册
     */
    'instance_registrars' => [
        'main' => [
            /** serviceName */
            get_env('NACOS_SERVICE_NAME', 'webman-service'),

            /** ip */
            get_env('NACOS_SERVICE_IP', Tools::getLocalIp()),

            /** port */
            (int)get_env('NACOS_SERVICE_PORT', Tools::getLocalServerPort()),

            /** optional @see \Workbunny\WebmanNacos\Provider\InstanceProvider::registerAsync() */
            [
                'groupName' => $groupName,
                'namespaceId' => $namespaceId,
                'enabled' => null,
                'ephemeral' => null,
            ],
        ],
        # 以下可以新增多个数组
    ]
];