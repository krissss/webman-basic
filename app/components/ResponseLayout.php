<?php

namespace app\components;

use Webman\Http\Response;
use WebmanTech\DTO\Attributes\ToArrayConfig;
use WebmanTech\DTO\BaseDTO;
use WebmanTech\DTO\BaseResponseDTO;

#[ToArrayConfig(emptyArrayAsObject: true)]
final class ResponseLayout extends BaseDTO
{
    public function __construct(
        public int    $code,
        public string $msg,
        public mixed  $data,
    )
    {
    }

    private array $headers = [];

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 是否使用 toArray 作为 data 返回
     */
    private bool $useToArrayForData = true;

    public function useToArrayForData(bool $bool): self
    {
        $this->useToArrayForData = $bool;
        return $this;
    }

    /**
     * 是否使用 statusCode 作为 code
     */
    private bool $useStatusCode = false;

    public function useStatusCode(bool $bool): self
    {
        $this->useStatusCode = $bool;
        return $this;
    }

    public static function fromInfo(int $code, string $msg, mixed $data, array $headers = []): self
    {
        return (new self(
            code: $code,
            msg: $msg,
            data: $data,
        ))->withHeaders($headers);
    }

    public static function fromResponseDTO(BaseResponseDTO $responseDTO): self
    {
        $code = $responseDTO->getResponseStatus();
        return self::fromInfo(
            code: $code,
            msg: $responseDTO->getResponseStatusText() ?? ($code === 200 ? 'ok' : ''),
            data: $responseDTO->toArray(),
            headers: $responseDTO->getResponseHeaders(),
        );
    }

    public function toJsonResponse(): Response
    {
        $data = $this->useToArrayForData ? $this->toArray() : ($this->data === [] ? new \stdClass() : $this->data);
        $response = json($data)->withHeaders($this->headers);
        if ($this->useStatusCode) {
            $response->withStatus($this->code, $this->msg ?: null);
        }
        return $response;
    }
}
