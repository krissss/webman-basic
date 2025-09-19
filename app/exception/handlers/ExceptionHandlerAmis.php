<?php

namespace app\exception\handlers;

use Illuminate\Validation\ValidationException;
use support\facade\Logger;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Exceptions\ValidationException as AmisValidationException;

class ExceptionHandlerAmis extends ExceptionHandler
{
    protected string $logChannel = Logger::CHANNEL_APP_AMIS;

    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);

        $this->dontReport = array_merge($this->dontReport, [
            AmisValidationException::class,
        ]);
    }

    protected array $extraInfos = [];

    protected function solveException(Throwable $exception): void
    {
        if ($exception instanceof AmisValidationException) {
            $this->statusCode = $exception->getCode();
            $this->statusMsg = '';
            $this->extraInfos = [
                'errors' => $exception->errors,
            ];

            return;
        }
        if ($exception instanceof ValidationException) {
            $this->statusCode = 422;
            $this->statusMsg = '';
            $this->extraInfos = [
                'errors' => array_map(fn($messages) => $messages[0], $exception->validator->errors()->toArray()),
            ];

            return;
        }

        parent::solveException($exception);
    }

    protected function buildResponse(Request $request, Throwable $exception): Response
    {
        $extra = array_merge([
            'status' => $this->statusCode,
            'response_data' => $this->responseData,
        ], $this->extraInfos);

        return amis_response([], $this->statusMsg, $extra);
    }
}
