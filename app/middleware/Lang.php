<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 语言设置
 */
class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if ($lang = session('lang')) {
            locale($lang);
        }
        /** @var Response $response */
        $response = $handler($request);

        $response->withHeader('Content-Language', locale());

        return $response;
    }
}
