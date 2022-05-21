<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 跨域访问
 */
class Cors implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if ($request->method() === 'OPTIONS') {
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
