<?php

namespace app\admin\controller;

use support\facade\Auth;
use Webman\Http\Response;

class InfoController
{
    // 登录用户信息
    public function index(): Response
    {
        return json_success(Auth::guardAdmin()->getUser());
    }
}
