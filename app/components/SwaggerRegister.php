<?php

namespace app\components;

use OpenApi\Annotations as OA;
use WebmanTech\Swagger\Swagger;

class SwaggerRegister
{
    public static function registerRouteApi(string $scanPath)
    {
        (new Swagger())->registerRoute([
            'openapi_doc' => [
                'scan_path' => $scanPath,
                'scan_exclude' => [
                    // 注释掉下面这个，可以看到 openapi 的例子
                    'ExampleSourceController.php',
                ],
                'modify' => function (OA\OpenApi $openapi) {
                    $openapi->info->title = config('app.name').' API';
                    $openapi->info->version = '1.0.0';
                    $openapi->servers = [
                        new OA\Server([
                            'url' => '/api',
                            'description' => 'localhost',
                        ]),
                    ];
                    if (!$openapi->components instanceof OA\Components) {
                        $openapi->components = new OA\Components([]);
                    }
                    $openapi->components->securitySchemes = [
                        new OA\SecurityScheme([
                            'securityScheme' => 'api_key',
                            'type' => 'apiKey',
                            'in' => 'header',
                            'name' => 'X-Api-Key',
                        ]),
                    ];
                },
            ],
        ]);
    }
}
