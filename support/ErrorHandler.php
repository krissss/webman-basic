<?php

namespace support;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Kriss\WebmanAuth\Exceptions\UnauthorizedException;
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
        if ($e instanceof UnauthorizedException) {
            $this->errorMessage = $e->getMessage();
            $this->statusCode = $e->getCode();
            return;
        }
        if ($e instanceof ModelNotFoundException) {
            $this->errorMessage = 'Data Not Found';
            $this->statusCode = 404;
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
