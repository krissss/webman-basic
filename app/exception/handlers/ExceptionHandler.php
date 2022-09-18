<?php

namespace app\exception\handlers;

use app\exception\UserSeeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use support\Log;
use Throwable;
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
    protected string $statusMsg = '';
    protected array $responseData = [];

    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);
        $this->_logger = Log::channel('app');
    }

    /**
     * @inheritdoc
     */
    public function render(Request $request, Throwable $exception): Response
    {
        $this->addDebugInfoToResponseData($request, $exception);
        $this->solveException($exception);

        return $this->buildResponse($request, $exception);
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     */
    protected function addDebugInfoToResponseData(Request $request, Throwable $exception)
    {
        if (!$this->_debug) {
            return;
        }
        if (!$request->expectsJson()) {
            return;
        }

        $this->responseData['error_message'] = $exception->getMessage();
        $this->responseData['error_trace'] = explode("\n", $exception->getTraceAsString());
    }

    /**
     * @param Throwable $exception
     */
    protected function solveException(Throwable $exception)
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
            //$this->responseData['errors'] = $exception->validator->errors()->all();
            return;
        }
        if ($exception instanceof UnauthorizedException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = $exception->getMessage();
            return;
        }
        if ($exception instanceof ModelNotFoundException) {
            $this->statusCode = 'Data Not Found';
            $this->statusMsg = 404;
            return;
        }

        $this->statusCode = 500;
        $this->statusMsg = $this->_debug ? $exception->getMessage() : 'Server internal error';
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    protected function buildResponse(Request $request, Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            return json_error($this->statusMsg, $this->statusCode, $this->responseData);
        }
        $error = $this->_debug ? \nl2br((string)$exception) : ($this->statusMsg ?: 'Server internal error');
        return new Response($this->statusCode, [], $error);
    }
}
