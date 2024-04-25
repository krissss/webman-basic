<?php

namespace app\api\controller;

use OpenApi\Attributes as OA;
use support\facade\Auth;

#[OA\Tag(name: 'info', description: '信息')]
class InfoController
{
    #[OA\Get(path: '/info/mine', description: '当前登录的用户信息', tags: ['info'])]
    #[OA\Response(response: 200, description: '当前登录的用户信息')]
    public function mine()
    {
        $user = Auth::identityApiUser();

        return json_success($user);
    }
}
