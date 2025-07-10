<?php

namespace app\api\controller;

use app\middleware\AuthenticateApi;
use app\model\User;
use OpenApi\Attributes as OA;
use support\facade\Auth;
use Webman\Annotation\Middleware;
use Webman\Http\Response;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[OA\Tag(name: 'info')]
#[Middleware(AuthenticateApi::class)]
final class InfoController
{
    #[OA\Get(
        path: '/info/mine',
        summary: '当前登录的用户信息',
        x: [
            SchemaConstants::X_SCHEMA_RESPONSE => User::class,
        ],
    )]
    public function mine(): Response
    {
        $user = Auth::identityApiUser();

        return json_success($user);
    }
}
