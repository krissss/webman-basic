<?php

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use support\Container;

return [
    'enable' => true,
    'laravel' => [
        /**
         * 如果用到 Laravel UploadedFile 中的 store 或 storeAs 相关方法，需要提供 filesystemFactory 实现
         */
        'filesystem' => function (): FilesystemFactory {
            if (class_exists(\WebmanTech\LaravelFilesystem\Facades\Storage::class)) {
                return \WebmanTech\LaravelFilesystem\Facades\Storage::instance();
            }
            if (!Container::has(FilesystemFactory::class)) {
                throw new InvalidArgumentException('请先配置 FilesystemFactory 实现');
            }
            return Container::get(FilesystemFactory::class);
        },
        /**
         * 如果用到 Laravel Request 中的 validate，需要提供 validationFactory 实现
         */
        'validation' => function (): ValidationFactory {
            if (class_exists(\WebmanTech\LaravelValidation\Facades\Validator::class)) {
                return \WebmanTech\LaravelValidation\Facades\Validator::instance();
            }
            if (function_exists('validator')) {
                $validator = validator();
                if ($validator instanceof ValidationFactory) {
                    return $validator;
                }
            }
            if (!Container::has(ValidationFactory::class)) {
                throw new InvalidArgumentException('请先配置 ValidationFactory 实现');
            }
            return Container::get(ValidationFactory::class);
        }
    ],
];
