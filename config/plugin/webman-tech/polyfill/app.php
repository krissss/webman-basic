<?php

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use support\Container;
use support\facade\Validator;

return [
    'enable' => true,
    'laravel' => [
        /**
         * 如果用到 Laravel UploadedFile 中的 store 或 storeAs 相关方法，需要提供 filesystemFactory 实现
         */
        'filesystem' => function (): FilesystemFactory {
            return Container::get(FilesystemFactory::class);
        },
        /**
         * 如果用到 Laravel Request 中的 validate，需要提供 validationFactory 实现
         */
        'validation' => function (): ValidationFactory {
            return Validator::instance();
        }
    ],
];
