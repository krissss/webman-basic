<?php

namespace app\admin\controller;

use OpenApi\Generator;
use Webman\Http\Response;

class OpenApiController
{
    public function index(): Response
    {
        return view('openapi/index', [
            'assetBasePath' => 'https://unpkg.com/swagger-ui-dist@4.5.0',
            'url' => '/admin/openapi/doc'
        ]);
    }

    public function doc(): Response
    {
        $openapi = Generator::scan([__DIR__ . '/../']);

        return response($openapi->toYaml(), 200, [
            'Content-Type' => 'application/x-yaml',
        ]);
    }
}
