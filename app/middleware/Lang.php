<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 语言设置
 */
class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if ($lang = session('lang')) {
            locale($lang);
        } elseif ($lang = $request->header('Accept-Language')) {
            locale($lang);
        }
        /** @var Response $response */
        $response = $handler($request);

        $response->withHeader('Content-Language', locale());

        return $response;
    }
}
