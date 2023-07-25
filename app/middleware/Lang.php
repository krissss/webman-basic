<?php

namespace app\middleware;

use app\enums\common\LangEnum;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 语言设置.
 */
class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $lang = session('lang');
        if (!$lang) {
            // $lang = $request->header('Accept-Language');
        }
        if ($lang && \in_array($lang, LangEnum::getValues())) {
            locale($lang);
        }
        /** @var Response $response */
        $response = $handler($request);

        $response->withHeader('Content-Language', locale());

        return $response;
    }
}
