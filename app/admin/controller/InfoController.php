<?php

namespace app\admin\controller;

use OpenApi\Annotations as OA;
use support\facade\Auth;
use Webman\Http\Response;

/**
 * @OA\Tag(name="info", description="当前登录用户信息")
 */
class InfoController
{
    /**
     * 登录用户信息
     *
     * 获取当前登录的用户的信息
     *
     * @OA\Get(
     *     path="/info",
     *     tags={"info"},
     *     security={{"api_key": {}}},
     *     @OA\Response(response="200",description="ok")
     * )
     */
    public function index(): Response
    {
        return json_success(Auth::guard()->getUser());
    }
}
