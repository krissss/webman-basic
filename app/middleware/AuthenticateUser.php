<?php

namespace app\middleware;

use Kriss\WebmanAuth\Middleware\Authentication;
use Webman\Http\Request;
use Webman\Http\Response;

class AuthenticateUser extends Authentication
{
    /**
     * @inheritDoc
     */
    protected function optionalRoutes(): array
    {
        return [
            route('user.login.view'),
            route('user.login'),
            route('user.layout'),
            route('user.logout'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        // 设定 app 为 user，否则异常不会传递到 ErrorHandleAmis 处理
        $request->app = 'user';
        return parent::process($request, $handler);
    }
}
