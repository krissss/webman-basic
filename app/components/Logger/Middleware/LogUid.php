<?php
namespace app\components\Logger\Middleware;

use app\components\Logger\Processors\WebUidProcessor;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class LogUid implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->{WebUidProcessor::REQUEST_ATTRIBUTE} = WebUidProcessor::generateUid2();

        return $handler($request);
    }
}
