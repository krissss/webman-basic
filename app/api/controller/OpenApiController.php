<?php

namespace app\api\controller;

use app\components\Component;
use app\components\Tools;
use app\middleware\OuterHostLimit;
use OpenApi\Generator;
use Symfony\Component\Finder\Finder;
use Webman\Http\Response;
use Webman\Route;

/**
 * Openapi 的路由地址
 * 如果要在 api2 下建立 openapi 访问，如下操作：
 * 1.在 api2/controller 下新建一个 OpenApiController 继承当前类
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
     * 扫描的路径，相对根目录
     */
    protected const SCAN_PATH = ['/app/api'];
    /**
     * 是否展示 Example 的例子
     * 为 null 时 debug 模式下启用，否则关闭
     */
    protected const ENABLE_EXAMPLE = null;

    public function index(): Response
    {
        $assetBasePath = 'https://unpkg.com/swagger-ui-dist@4.5.0';

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
                        url: window.location.pathname + '/doc',
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
        $filename = md5(static::class);
        $filepath = runtime_path() . '/openapi/' . $filename . '.yaml';
        Tools::makeDirectory($filepath);
        $recordKey = [__CLASS__, __FUNCTION__, 'v1'];
        if (!file_exists($filepath) || !Component::memoryRemember()->get($recordKey)) {
            $openapi = Generator::scan($this->getScanPaths());
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

        Route::get('/openapi', [static::class, 'index'])->middleware(OuterHostLimit::class);
        Route::get('/openapi/doc', [static::class, 'doc'])->middleware(OuterHostLimit::class);
    }

    protected function getScanPaths(): Finder
    {
        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(array_map(fn($path) => base_path() . $path, static::SCAN_PATH));
        if (!(static::ENABLE_EXAMPLE ?? config('app.debug'))) {
            $finder->notName('ExampleSourceController.php');
        }
        return $finder;
    }
}
