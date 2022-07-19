<?php

namespace app\middleware;

use Kriss\WebmanAuth\Middleware\Authentication;
use Webman\Http\Request;
use Webman\Http\Response;

class AuthenticateAdmin extends Authentication
{
    /**
     * @inheritDoc
     */
    protected function optionalRoutes(): array
    {
        return [
            route('admin.login.view'),
            route('admin.login'),
            route('admin.layout'),
            route('admin.logout'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        // 设定 app 为 admin，否则异常不会传递到 ErrorHandleAdmin 处理
        $request->app = 'admin';
        return parent::process($request, $handler);
    }
}
