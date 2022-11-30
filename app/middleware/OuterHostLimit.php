<?php

namespace app\middleware;

use Illuminate\Support\Str;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class OuterHostLimit implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $host = $request->host();
        if (!Str::contains($host, [
            '127.0.0.1',
            'localhost',
            '192.168.',
            '172.16.',
            '10.',
            'gateway',
        ])) {
            return response('Forbidden not inner network!!', 403);
        }

        return $handler($request);
    }
}
