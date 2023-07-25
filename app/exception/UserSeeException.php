<?php

namespace app\exception;

class UserSeeException extends \Exception
{
    protected array $data = [];

    public function __construct($message = 'Unprocessable Entity', $code = 422, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function withData(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
