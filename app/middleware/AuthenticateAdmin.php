<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\Auth\Middleware\Authentication;

class AuthenticateAdmin extends Authentication
{
    /**
     * {@inheritDoc}
     */
    protected function optionalRoutes(): array
    {
        return [
            'admin.login.view',
            'admin.login',
            'admin.layout',
            'admin.logout',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, callable $handler): Response
    {
        // 设定 app 为 admin，否则异常不会传递到 ErrorHandleAmis 处理
        $request->app = 'admin';

        return parent::process($request, $handler);
    }
}
