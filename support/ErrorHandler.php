<?php

namespace support;

use Illuminate\Validation\ValidationException;
use Throwable;
use Tinywan\ExceptionHandler\Handler;
use Webman\Http\Response;

class ErrorHandler extends Handler
{
    /**
     * @inheritDoc
     */
    protected function solveExtraException(Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            $this->errorMessage = $e->validator->errors()->first();
            $this->statusCode = 422;
            return;
        }

        parent::solveExtraException($e);
    }

    /**
     * @inheritDoc
     */
    protected function buildResponse(): Response
    {
        return json_error(
            $this->errorMessage,
            $this->statusCode,
            $this->responseData,
        );
    }
}
