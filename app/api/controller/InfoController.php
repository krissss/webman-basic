<?php

namespace app\api\controller;

use OpenApi\Annotations as OA;
use support\facade\Auth;

/**
 * @OA\Tag(name="info", description="信息")
 */
class InfoController
{
    /**
     * 当前登录的用户信息.
     *
     * @OA\Get(
     *     path="/info/mine",
     *     tags={"info"},
     *
     *     @OA\Response(response=200, description="当前登录的用户信息"),
     *     security={{"api_key": {}}},
     * )
     */
    public function mine()
    {
        $user = Auth::identityApiUser();

        return json_success($user);
    }
}
