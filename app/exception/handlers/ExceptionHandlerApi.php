<?php

namespace app\exception\handlers;

use support\facade\Logger;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\LaravelCache\Exceptions\ThrottleRequestsException;

class ExceptionHandlerApi extends ExceptionHandler
{
    protected string $logChannel = Logger::CHANNEL_APP_API;

    protected function solveException(Throwable $exception): void
    {
        if ($exception instanceof ThrottleRequestsException) {
            $this->statusCode = $exception->getStatusCode();
            $this->statusMsg = $exception->getMessage();
            $this->headers = $exception->getHeaders();

            return;
        }

        parent::solveException($exception);
    }

    protected function buildResponse(Request $request, Throwable $exception): Response
    {
        return json_error($this->statusMsg, $this->statusCode, $this->responseData, $this->headers);
    }
}
