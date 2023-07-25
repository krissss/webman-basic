<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\Auth\Middleware\Authentication;

class AuthenticateUser extends Authentication
{
    /**
     * {@inheritDoc}
     */
    protected function optionalRoutes(): array
    {
        return [
            'user.login.view',
            'user.login',
            'user.layout',
            'user.logout',
            'user.login.admin',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, callable $handler): Response
    {
        // 设定 app 为 user，否则异常不会传递到 ErrorHandleAmis 处理
        $request->app = 'user';

        return parent::process($request, $handler);
    }
}
