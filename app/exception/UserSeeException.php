<?php

namespace app\exception;

use Tinywan\ExceptionHandler\Exception\BaseException;

class UserSeeException extends BaseException
{
    public $statusCode = 422;
    public $errorMessage = 'Unprocessable Entity';
}
