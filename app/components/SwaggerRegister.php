<?php

namespace app\components;

use OpenApi\Attributes as OA;
use OpenApi\Annotations as OAA;
use WebmanTech\Swagger\Swagger;

class SwaggerRegister
{
    public static function registerRouteApi(string $scanPath)
    {
        (new Swagger())->registerRoute([
            'register_webman_route' => true,
            'openapi_doc' => [
                'scan_path' => $scanPath,
                'scan_exclude' => [
                    // 注释掉下面这个，可以看到 openapi 的例子
                    'ExampleSourceController.php',
                    'InfoController.php',
                ],
                'modify' => function (OAA\OpenApi $openapi) {
                    $openapi->info->title = config('app.name') . ' API';
                    $openapi->info->version = '1.0.0';
                    $openapi->servers = [
                        new OA\Server(
                            url: route_url('/api'),
                            description: request()->host(),
                        ),
                    ];
                    if (!$openapi->components instanceof OAA\Components) {
                        $openapi->components = new OAA\Components([]);
                    }
                    $openapi->components->securitySchemes = [
                        new OA\SecurityScheme(
                            securityScheme: 'api_key',
                            type: 'apiKey',
                            name: 'X-Api-Key',
                            in: 'header',
                        ),
                    ];
                },
            ],
        ]);
    }
}
