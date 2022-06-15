<?php

namespace app\admin\controller;

use OpenApi\Generator;
use Webman\Http\Response;
use Webman\Route;

/**
 * Openapi 的路由地址
 * 如果要在 api 下建立 openapi 访问，如下操作：
 * 1.在 api/controller 下新建一个 OpenApiController 继承当前类
 * 2.修改 const 常量配置
 * 3.在 config/route.php 下相应增加路由注册
 */
class OpenApiController
{
    /**
     * 当配置文件不配置时，默认是否启用
     */
    protected const DEFAULT_ENABLE = false;
    /**
     * 路由名称
     */
    protected const ROUTE_NAME = 'openapi.doc';
    /**
     * 扫描的路径
     */
    protected const SCAN_PATH = '/app/admin';

    public function index(): Response
    {
        $assetBasePath = 'https://unpkg.com/swagger-ui-dist@4.5.0';
        $url = route(static::ROUTE_NAME);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
        name="description"
        content="SwaggerUI"
    />
    <title>SwaggerUI</title>
    <link rel="stylesheet" href="{$assetBasePath}/swagger-ui.css" />
</head>
<body>
<div id="swagger-ui"></div>
<script src="{$assetBasePath}/swagger-ui-bundle.js" crossorigin></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            // @link https://github.com/swagger-api/swagger-ui/blob/master/docs/usage/configuration.md
            dom_id: '#swagger-ui',
            url: '{$url}',
            filter: '',
        });
    };
</script>
</body>
</html>
HTML;
        return response($html);
    }

    public function doc(): Response
    {
        $openapi = Generator::scan([base_path() . static::SCAN_PATH]);

        return response($openapi->toYaml(), 200, [
            'Content-Type' => 'application/x-yaml',
        ]);
    }

    public static function registerRoute()
    {
        if (!get_env('OPENAPI_ENABLE', static::DEFAULT_ENABLE)) {
            return;
        }

        Route::get('/openapi', [static::class, 'index']);
        Route::get('/openapi/doc', [static::class, 'doc'])->name(static::ROUTE_NAME);
    }
}
