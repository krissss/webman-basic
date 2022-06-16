<?php

namespace app\admin\controller;

use app\components\Component;
use app\components\Tools;
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
     * 当 env 不配置时，默认是否启用
     * 为 null 时 debug 模式下启用，否则关闭
     */
    protected const DEFAULT_ENABLE = null;
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
            persistAuthorization: true,
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
        $filepath = runtime_path() . '/openapi/' . static::ROUTE_NAME . '.yaml';
        Tools::makeDirectory($filepath);
        $recordKey = [__CLASS__, __FUNCTION__, 'v1'];
        if (!file_exists($filepath) || !Component::memoryRemember()->get($recordKey)) {
            $openapi = Generator::scan([base_path() . static::SCAN_PATH]);
            $yaml = $openapi->toYaml();
            file_put_contents($filepath, $yaml);
            Component::memoryRemember()->set($recordKey, 1);
        } else {
            $yaml = file_get_contents($filepath);
        }

        return response($yaml, 200, [
            'Content-Type' => 'application/x-yaml',
        ]);
    }

    public static function registerRoute()
    {
        if (!get_env('OPENAPI_ENABLE', static::DEFAULT_ENABLE ?? config('app.debug'))) {
            return;
        }

        Route::get('/openapi', [static::class, 'index']);
        Route::get('/openapi/doc', [static::class, 'doc'])->name(static::ROUTE_NAME);
    }
}
