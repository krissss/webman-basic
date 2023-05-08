<?php

return [
    'enable' => get_env('OPENAPI_ENABLE', true),
    'global_route' => [
        /**
         * 全局扫描的配置
         * @see \WebmanTech\Swagger\Swagger::registerGlobalRoute()
         */
        'enable' => false,
    ],
    'host_forbidden' => [
        /**
         * 全局的 host forbidden 配置
         * @see \WebmanTech\Swagger\Middleware\HostForbiddenMiddleware::$config
         */
        'enable' => true,
        'host_white_list' => [
            'gateway'
        ],
    ],
    'swagger_ui' => [
        /**
         * 全局的 swagger ui 配置
         * @see \WebmanTech\Swagger\Controller\OpenapiController::swaggerUI()
         */
    ],
    'openapi_doc' => [
        /**
         * 全局的 openapi doc 配置
         * @see \WebmanTech\Swagger\Controller\OpenapiController::openapiDoc()
         */
    ],
];
