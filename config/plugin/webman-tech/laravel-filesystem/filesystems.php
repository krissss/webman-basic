<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path() . '/app',
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path() . '/app/public',
            'url' => get_env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => get_env('AWS_ACCESS_KEY_ID'),
            'secret' => get_env('AWS_SECRET_ACCESS_KEY'),
            'region' => get_env('AWS_DEFAULT_REGION'),
            'bucket' => get_env('AWS_BUCKET'),
            'url' => get_env('AWS_URL'),
            'endpoint' => get_env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => get_env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
        'oss' => [
            // 配置与使用：https://github.com/iiDestiny/laravel-filesystem-oss
            'driver' => 'oss',
            'root' => '', // 设置上传时根前缀
            'access_key' => get_env('OSS_ACCESS_KEY'),
            'secret_key' => get_env('OSS_SECRET_KEY'),
            'endpoint' => get_env('OSS_ENDPOINT'), // 使用 ssl 这里设置如: https://oss-cn-beijing.aliyuncs.com
            'bucket' => get_env('OSS_BUCKET'),
            'isCName' => get_env('OSS_IS_CNAME', false), // 如果 isCname 为 false，endpoint 应配置 oss 提供的域名如：`oss-cn-beijing.aliyuncs.com`，否则为自定义域名，，cname 或 cdn 请自行到阿里 oss 后台配置并绑定 bucket
            // 如果有更多的 bucket 需要切换，就添加所有bucket，默认的 bucket 填写到上面，不要加到 buckets 中
            'buckets' => [
                'test' => [
                    'access_key' => get_env('OSS_ACCESS_KEY'),
                    'secret_key' => get_env('OSS_SECRET_KEY'),
                    'bucket' => get_env('OSS_TEST_BUCKET'),
                    'endpoint' => get_env('OSS_TEST_ENDPOINT'),
                    'isCName' => get_env('OSS_TEST_IS_CNAME', false),
                ],
                //...
            ],
        ],
        'qiniu' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-qiniu
            'driver' => 'qiniu',
            'access_key' => get_env('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),
            'secret_key' => get_env('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),
            'bucket' => get_env('QINIU_BUCKET', 'test'),
            'domain' => get_env('QINIU_DOMAIN', 'xxx.clouddn.com'), // or host: https://xxxx.clouddn.com
        ],
        'cos' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-cos
            'driver' => 'cos',
            'app_id'     => get_env('COS_APP_ID'),
            'secret_id'  => get_env('COS_SECRET_ID'),
            'secret_key' => get_env('COS_SECRET_KEY'),
            'region'     => get_env('COS_REGION', 'ap-guangzhou'),
            'bucket'     => get_env('COS_BUCKET'),  // 不带数字 app_id 后缀
            // 可选，如果 bucket 为私有访问请打开此项
            'signed_url' => false,
            // 可选，是否使用 https，默认 false
            'use_https' => true,
            // 可选，自定义域名
            'domain' => 'emample-12340000.cos.test.com',
            // 可选，使用 CDN 域名时指定生成的 URL host
            'cdn' => get_env('COS_CDN'),
            'prefix' => get_env('COS_PATH_PREFIX'), // 全局路径前缀
            'guzzle' => [
                'timeout' => get_env('COS_TIMEOUT', 60),
                'connect_timeout' => get_env('COS_CONNECT_TIMEOUT', 60),
            ],
        ],
    ],
    'links' => [
        public_path() . '/storage' => storage_path() . '/app/public',
    ],
    'extends' => [
        // 其他文件系统的扩展
        'oss' => \WebmanTech\LaravelFilesystem\Extend\OssExtend::class,
        'qiniu' => \WebmanTech\LaravelFilesystem\Extend\QiNiuExtend::class,
        'cos' => \WebmanTech\LaravelFilesystem\Extend\CosExtend::class,
    ],
];
