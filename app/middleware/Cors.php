<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 跨域访问.
 */
class Cors implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if ('OPTIONS' === $request->method()) {
            return response();
        }

        $response = $handler($request);
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Headers' => '*',
        ]);

        return $response;
    }
}
