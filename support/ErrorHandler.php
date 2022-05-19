<?php

namespace support;

use Illuminate\Validation\ValidationException;
use Throwable;
use Tinywan\ExceptionHandler\Event\DingTalkRobotEvent;
use Tinywan\ExceptionHandler\Exception\BaseException;
use Tinywan\ExceptionHandler\Handler;
use Webman\Http\Request;
use Webman\Http\Response;

class ErrorHandler extends \support\overwrite\ErrorHandler
{
    /**
     * @inheritDoc
     */
    protected function solveExtraException(Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            $this->exceptionInfo['errorMsg'] = $e->validator->errors()->first();
            $this->exceptionInfo['statusCode'] = 422;
            return;
        }

        parent::solveExtraException($e);
    }

    /**
     * 构造 Response
     * @return Response
     */
    protected function buildResponse(): Response
    {
        return  json_error(
            $this->exceptionInfo['errorMsg'],
            $this->exceptionInfo['statusCode'],
            $this->responseData,
        );
    }
}
