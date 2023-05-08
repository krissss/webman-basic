<?php

use OpenApi\Annotations as OA;
use support\facade\Route;
use WebmanTech\Auth\Middleware\Authentication;
use WebmanTech\Auth\Middleware\SetAuthGuard;
use WebmanTech\LaravelCache\Middleware\ThrottleRequestsFactory;
use WebmanTech\Swagger\Swagger;

(new Swagger())->registerRoute([
    'openapi_doc' => [
        'scan_path' => __DIR__ . '/controller',
        'scan_exclude' => [
            // 注释掉下面这个，可以看到 openapi 的例子
            'ExampleSourceController.php',
        ],
        'modify' => function (OA\OpenApi $openapi) {
            $openapi->info->title = config('app.name') . ' API';
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
                ])
            ];
        }
    ],
]);

// 请勿将以下注释打开使用，仅做参考使用
//Route::resource('crud', \app\api\controller\ExampleSourceController::class, ['name_prefix' => 'api.']);

Route::group('', function () {
    // 需要授权的
    Route::get('/info/mine', [\app\api\controller\InfoController::class, 'mine']);
})->middleware([
    fn() => new SetAuthGuard('api_user'),
    Authentication::class,
    ThrottleRequestsFactory::class,
]);
