<?php

namespace app\middleware;

use support\facade\Logger;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use Yiisoft\Strings\WildcardPattern;

class OperateLogMiddleware implements MiddlewareInterface
{
    protected string $logChannel = Logger::CHANNEL_OPERATE_LOG;
    protected array $skipRequest = [
        '/_debugbar/*',
        '/admin/log-reader*',
    ];

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        if ($this->isSkipRequest($request)) {
            return $handler($request);
        }

        $data = [
            'request' => [
                //'host' => $request->host(),
                'method' => $request->method(),
                'uri' => $request->uri(),
                'body' => $request->file() ? '__withFiles__' : $request->rawBody(),
            ],
            'response' => [],
        ];

        try {
            /** @var Response $response */
            $response = $handler($request);
        } catch (\Throwable $e) {
            $response = new Response(9999);
            $response->exception($e);
        }
        $data['response'] = array_filter([
            'status' => $response->getStatusCode(),
            'exception' => $response->exception() ? $response->exception()->getMessage() : null,
        ]);

        Logger::withChannel($this->logChannel, $data);

        if ($response->getStatusCode() === 9999 && $response->exception()) {
            throw $response->exception();
        }

        return $response;
    }

    protected function isSkipRequest(Request $request): bool
    {
        $path = $request->path();
        foreach ($this->skipRequest as $pattern) {
            if ((new WildcardPattern($pattern, []))->match($path)) {
                return true;
            }
        }
        return false;
    }
}
