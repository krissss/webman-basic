<?php

namespace app\admin;

use OpenApi\Annotations as OA;

/**
 * 参考
 * @link https://github.com/zircote/swagger-php/blob/master/Examples/petstore.swagger.io
 *
 * 基础信息
 * @link https://swagger.io/specification/#info-object
 * @OA\OpenApi(
 *     @OA\Info(version="1.0.0", title="App openAPI"),
 *     @OA\Server(url="/admin", description="local"),
 *     @OA\Server(url="/{service_name}/admin", description="gateway"),
 * )
 *
 * 授权方式
 * @link https://swagger.io/specification/#security-requirement-object
 * @OA\SecurityScheme(
 *     securityScheme="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="X-Api-Key"
 * )
 */
class OpenApiSpec
{
}
