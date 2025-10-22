<?php

namespace app\exception;

class UserSeeException extends \Exception
{
    protected array $data = [];

    public function __construct(?string $message = null, int $code = 422, ?\Throwable $previous = null)
    {
        $message ??= 'Unprocessable Entity';
        parent::__construct($message, $code, $previous);
    }

    public static function throwWithData(array $data, ?string $message = null)
    {
        throw (new self(message: $message))->withData($data);
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
