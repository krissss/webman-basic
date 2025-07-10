<?php

return [
    'enable' => get_env('OPENAPI_ENABLE', true),
    /**
     * 全局扫描的配置
     * @see \WebmanTech\Swagger\Swagger::registerGlobalRoute()
     * @see \WebmanTech\Swagger\DTO\ConfigRegisterRouteDTO
     */
    'global_route' => [
        'enable' => false,
        'register_route' => false,
    ],
    /**
     * 全局的 host forbidden 配置
     * @see \WebmanTech\Swagger\DTO\ConfigHostForbiddenDTO
     */
    'host_forbidden' => [
        'enable' => get_env('OPENAPI_HOST_FORBIDDEN', true),
    ],
    /**
     * 全局的 swagger ui 配置
     * @see \WebmanTech\Swagger\DTO\ConfigSwaggerUiDTO
     */
    'swagger_ui' => [
        // 在 composer.json 中配置资源下载后，可以本地化访问：
        // {
        //        "type": "npm",
        //        "name": "swagger-ui-dist",
        //        "version": "5.26.2",
        //        "only_files": [
        //          "swagger-ui.css",
        //          "swagger-ui-bundle.js"
        //        ]
        //      }
        //'assets_base_url' => '/assets/swagger-ui-dist@5.26.2'
    ],
    /**
     * 全局的 openapi doc 配置
     * @see \WebmanTech\Swagger\DTO\ConfigOpenapiDocDTO
     */
    'openapi_doc' => [
        'cache_key' => fn() => route_url(''),
        'format' => 'json',
        'schema_name_format_use_classname' => true,
        'clean_unused_components_enable' => true,
    ],
];
