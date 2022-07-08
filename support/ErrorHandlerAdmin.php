<?php

namespace support;

use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use Throwable;
use Webman\Http\Response;

class ErrorHandlerAdmin extends ErrorHandler
{
    public function __construct($logger, $debug)
    {
        parent::__construct($logger, $debug);
        $this->_logger = Log::channel('appAdmin');
    }

    protected array $extraInfos = [];

    protected function solveExtraException(Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            $this->statusCode = $e->getCode();
            $this->extraInfos = [
                'errors' => $e->errors,
            ];
            $this->errorMessage = '';
            return;
        }

        parent::solveExtraException($e);
    }

    /**
     * @inheritDoc
     */
    protected function buildResponse(): Response
    {
        $extra = array_merge([
            'status' => $this->statusCode,
        ], $this->extraInfos);

        return amis_response($this->responseData, $this->errorMessage, $extra);
    }
}
