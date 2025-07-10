<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\Auth\Middleware\Authentication;
use WebmanTech\Auth\Middleware\SetAuthGuard;

class AuthenticateApi extends Authentication
{
    /**
     * {@inheritDoc}
     */
    protected function optionalRoutes(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, callable $handler): Response
    {
        dump(123);
        $request->{SetAuthGuard::REQUEST_GUARD_NAME} = 'api_user';

        // 设定 app 为 user，否则异常不会传递到 ErrorHandleAmis 处理
        $request->app = 'api';

        return parent::process($request, $handler);
    }
}
