<?php

namespace app\exception\handlers;

use app\exception\UserSeeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use support\facade\Logger;
use support\Log;
use Webman\Exception\ExceptionHandler as BaseExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\Auth\Exceptions\UnauthorizedException;

class ExceptionHandler extends BaseExceptionHandler
{
    public $dontReport = [
        ValidationException::class,
        UnauthorizedException::class,
        ModelNotFoundException::class,
        UserSeeException::class,
    ];

    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $statusMsg = '';
    protected array $responseData = [];

    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);
        $this->logger = Log::channel(Logger::CHANNEL_APP);
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, \Throwable $exception): Response
    {
        $this->addDebugInfoToResponseData($request, $exception);
        $this->solveException($exception);

        return $this->buildResponse($request, $exception);
    }

    protected function addDebugInfoToResponseData(Request $request, \Throwable $exception)
    {
        if (!$this->debug) {
            return;
        }
        if (!$request->expectsJson()) {
            return;
        }

        $this->responseData['error_message'] = $exception->getMessage();
        $this->responseData['error_trace'] = explode("\n", $exception->getTraceAsString());
    }

    protected function solveException(\Throwable $exception)
    {
        if ($exception instanceof UserSeeException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = $exception->getMessage();
            $this->responseData = array_merge($this->responseData, $exception->getData());

            return;
        }
        if ($exception instanceof ValidationException) {
            $this->statusCode = $exception->status;
            $this->statusMsg = $exception->validator->errors()->first();
            // $this->responseData['errors'] = $exception->validator->errors()->all();
            return;
        }
        if ($exception instanceof UnauthorizedException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = $exception->getMessage();

            return;
        }
        if ($exception instanceof ModelNotFoundException) {
            $this->statusCode = 404;
            $this->statusMsg = 'Data Not Found';

            return;
        }

        $this->statusCode = 500;
        $this->statusMsg = $this->debug ? $exception->getMessage() : 'Server internal error';
    }

    protected function buildResponse(Request $request, \Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            return json_error($this->statusMsg, $this->statusCode, $this->responseData, $this->headers);
        }
        $error = $this->debug ? nl2br((string) $exception) : ($this->statusMsg ?: 'Server internal error');

        return new Response($this->statusCode, $this->headers, $error);
    }
}
