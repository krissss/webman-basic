<?php

namespace app\components;

use OpenApi\Annotations as OAA;
use OpenApi\Attributes as OA;
use Symfony\Component\Finder\Finder;
use WebmanTech\Swagger\Swagger;

final class SwaggerRegister
{
    public static function registerRouteApi(): void
    {
        Swagger::create()->registerRoute([
            'register_route' => true,
            /**
             * @see \WebmanTech\Swagger\DTO\ConfigOpenapiDocDTO
             */
            'openapi_doc' => [
                'scan_path' => fn() => [
                    __DIR__ . '/ResponseLayout.php',
                    Finder::create()->files()->name('*.php')
                        ->in(app_path('api/controller'))
                        ->exclude([
                            'form/example',
                        ])
                        ->notName([
                            'ExampleSourceController.php',
                            'InfoController.php',
                        ]),
                    Finder::create()->files()->name('*.php')
                        ->in(app_path('model'))
                        ->in(app_path('enums'))
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
                'response_layout_class' => ResponseLayout::class,
            ],
        ]);
    }
}
