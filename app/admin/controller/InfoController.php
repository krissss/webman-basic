<?php

namespace app\admin\controller;

use support\facade\Auth;
use Webman\Http\Response;

/**
 * 当前登录用户信息
 */
class InfoController
{
    /**
     * 登录用户信息
     * @return Response
     */
    public function index(): Response
    {
        return admin_response(Auth::guard()->getUser());
    }
}
