<?php

namespace app\exception\handlers;

use Illuminate\Validation\ValidationException as LaravelValidationException;
use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use support\Log;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class ExceptionHandlerAmis extends ExceptionHandler
{
    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);
        $this->_logger = Log::channel('appAdmin');

        $this->dontReport = array_merge($this->dontReport, [
            ValidationException::class,
            LaravelValidationException::class,
        ]);
    }

    protected array $extraInfos = [];

    /**
     * @inheritdoc
     */
    protected function solveException(Throwable $exception): void
    {
        if ($exception instanceof ValidationException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = '';
            $this->extraInfos = [
                'errors' => $exception->errors,
            ];
            return;
        }
        if ($exception instanceof LaravelValidationException) {
            $this->statusCode = 422;
            $this->statusMsg = '';
            $this->extraInfos = [
                'errors' => array_map(fn($messages) => $messages[0], $exception->validator->errors()->toArray())
            ];
            return;
        }

        parent::solveException($exception);
    }

    /**
     * @inheritDoc
     */
    protected function buildResponse(Request $request, Throwable $exception): Response
    {
        $extra = array_merge([
            'status' => $this->statusCode,
            'response_data' => $this->responseData,
        ], $this->extraInfos);

        return amis_response([], $this->statusMsg, $extra);
    }
}
