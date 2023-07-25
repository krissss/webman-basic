<?php

namespace app\exception;

class FileExistsException extends \Exception
{
    private string $path;

    public function __construct(string $path, $message = 'File already exist', $code = 0, \Throwable $previous = null)
    {
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
