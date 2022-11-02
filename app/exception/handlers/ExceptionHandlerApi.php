<?php

namespace app\exception\handlers;

use app\exception\UserSeeException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use support\facade\Logger;
use support\Log;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Exceptions\ValidationException;

class ExceptionHandlerApi extends ExceptionHandler
{
    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);
        $this->_logger = Log::channel(Logger::CHANNEL_APP_API);

        $this->dontReport = array_merge($this->dontReport, [
            ValidationException::class,
            LaravelValidationException::class,
            UserSeeException::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function solveException(Throwable $exception): void
    {
        if ($exception instanceof ValidationException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = $exception->errors[0];
            return;
        }
        if ($exception instanceof LaravelValidationException) {
            $this->statusCode = 422;
            $this->statusMsg = $exception->validator->errors()->first();
            return;
        }

        parent::solveException($exception);
    }

    /**
     * @inheritDoc
     */
    protected function buildResponse(Request $request, Throwable $exception): Response
    {
        return json_error($this->statusMsg, $this->statusCode, $this->responseData);
    }
}
