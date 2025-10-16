<?php

namespace app\middleware;

use DateTimeImmutable;
use support\facade\Auth;
use support\facade\Logger;
use support\Log;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Webman\Http\Request;
use Webman\Http\Request as WebmanRequest;
use Webman\Http\Response;
use Webman\Http\Response as WebmanResponse;
use Webman\MiddlewareInterface;

class OperateLogMiddleware implements MiddlewareInterface
{
    private static ?HttpRequestMessage $message = null;

    public function process(Request $request, callable $handler): Response
    {
        if (self::$message === null) {
            self::$message = new HttpRequestMessage(array_merge([
                'channel' => Logger::CHANNEL_OPERATE_LOG,
            ], get_env('LOG_OPERATE_LOG_CONFIG', [])));
        }
        $message = self::$message;
        try {
            $message->markRequestStart($request);
            $response = $handler($request);
            return $response;
        } catch (\Throwable $e) {
            $response = new Response(9999);
            $response->exception($e);
            return $response;
        } finally {
            $message->markResponseEnd($response ?? null);
        }
    }
}

/**
 * Http 请求日志
 */
class HttpRequestMessage
{
    use ClockAwareTrait;

    private bool $enable = true;
    private string $channel = 'httpRequest';
    private int $logMinTimeMS = 0; // 仅记录大于该时间的请求，单位毫秒
    private int $warningTimeMS = 2000; // 超过该时间，记为 warning
    private int $errorTimeMS = 10000; // 超过该时间，记为 error
    private array $skipPaths = [
        "/^\/\.well-known\/.*/i",
        "/^\/favicon.ico$/i",
        '/^\/_debugbar\/.*/i',
        '/^\/admin\/log-reader.*/i',
    ]; // 忽略的请求路径，使用正则
    /** @phpstan-ignore-next-line */
    private ?\Closure $skipRequest = null; // 忽略的请求，返回 true 表示忽略
    /** @phpstan-ignore-next-line */
    private ?\Closure $extraInfo = null; // 其他信息
    private bool $logRequestQuery = true; // 是否记录请求参数
    /** @phpstan-ignore-next-line */
    private ?\Closure $logRequestQueryFn = null; // 通过 callback 处理记录的 query
    private bool $logRequestBody = true; // 是否记录请求 body
    /** @phpstan-ignore-next-line */
    private ?\Closure $logRequestBodyFn = null; // 通过 callback 处理记录请求 body
    private array $logRequestBodySensitive = [
        'password',
        'password_confirmation',
        'password_confirm',
        'old_password',
        'new_password',
        'new_password_confirmation',
    ]; // requestBody 中的敏感字段
    private int $logRequestBodyLimitSize = 1000; // 记录请求 body 的最大长度
    private bool $supportAuthComponent = true; // 支持 webman-tech/auth 组件

    public function __construct(array $config = [])
    {
        $config = array_filter($config, fn($value) => $value !== null);
        foreach ($config as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }

    final public function appendSkipPaths(string|array $paths): static
    {
        $this->skipPaths = array_unique(array_merge($this->skipPaths, (array)$paths));

        return $this;
    }

    final public function appendLogRequestBodySensitive(string|array $keys): static
    {
        $this->logRequestBodySensitive = array_unique(array_merge($this->logRequestBodySensitive, (array)$keys));

        return $this;
    }

    private mixed $request = null;
    private ?DateTimeImmutable $start = null;

    public function markRequestStart(mixed $request): void
    {
        if (!$this->enable) {
            return;
        }

        $this->request = $request;
        $this->start = $this->now();
    }

    public function markResponseEnd(mixed $response): void
    {
        if (!$this->enable || !$this->request || !$this->start) {
            return;
        }

        $this->handle($this->request, $response);
        $this->request = null;
        $this->start = null;
    }

    public function handle(mixed $request, mixed $response): void
    {
        if (!$this->start) {
            return;
        }

        $logLevel = 'info';
        // 计算 cost
        $costDiff = $this->now()->diff($this->start);
        $cost = intval($costDiff->s * 1000 + $costDiff->f * 1000);
        // cost 太小的，忽略记录
        if ($cost < $this->logMinTimeMS) {
            return;
        }
        // 根据 cost 控制 level
        if ($this->warningTimeMS > 0 && $cost >= $this->warningTimeMS) {
            $logLevel = 'warning';
        }
        if ($this->errorTimeMS > 0 && $cost >= $this->errorTimeMS) {
            $logLevel = 'error';
        }

        // 获取 request 信息
        $requestMethod = match (true) {
            $request instanceof WebmanRequest => $request->method(),
            $request instanceof SymfonyRequest => $request->getMethod(),
            default => throw new \InvalidArgumentException(),
        };
        $requestPath = match (true) {
            /** @phpstan-ignore-next-line */
            $request instanceof WebmanRequest => ($request->route ? $request->route->getPath() : $request->path()),
            $request instanceof SymfonyRequest => $request->getPathInfo(),
            default => throw new \InvalidArgumentException(),
        };
        if ($this->shouldSkipRequestPath($requestPath)) {
            return;
        }
        if ($this->shouldSkipRequest($request)) {
            return;
        }

        // context 信息
        $message = $requestMethod . ':' . $requestPath;
        $context = [
            'cost' => $cost,
            'method' => $requestMethod,
            'path' => $requestPath,
        ];
        // auth 支持
        if ($this->supportAuthComponent && class_exists(Auth::class)) {
            try {
                $guard = Auth::guard();
            } catch (\Throwable) {
                $guard = null;
            }
            $context['user'] = $guard?->getId();
        }
        // 从 request 中获取信息
        if ($request instanceof WebmanRequest) {
            /** @var WebmanResponse $response */
            $context = array_merge(
                $context,
                [
                    'ip' => $request->getRealIp(false),
                    /** @phpstan-ignore-next-line */
                    'params' => $request->route?->param() ?: null,
                    'query' => $this->getRequestQuery($request),
                    'body' => $this->getRequestBody($request),
                    'response_status' => $response->getStatusCode(),
                    'response_exception' => $response->exception()?->getMessage(),
                ],
            );
            if ($response->exception()) {
                $logLevel = 'error';
            }
        } else {
            /** @var SymfonyRequest $request */
            /** @var SymfonyResponse $response */
            $context = array_merge(
                $context,
                [
                    'ip' => $request->getClientIp(),
                    'query' => $this->getRequestQuery($request),
                    'body' => $this->getRequestBody($request),
                    'response_status' => $response->getStatusCode(),
                ],
            );
        }
        // 根据 response 状态码控制 level
        $responseStatus = $context['response_status'];
        if ($responseStatus && $logLevel === 'info') {
            if ($responseStatus >= 500) {
                $logLevel = 'error';
            } elseif ($responseStatus >= 400) {
                $logLevel = 'warning';
            }
        }
        // 添加其他信息
        if ($this->extraInfo) {
            $context = array_merge($context, ($this->extraInfo)($request, $response));
        }

        Log::channel($this->channel)->log($logLevel, $message, array_filter($context, fn($v) => $v !== null));
    }

    protected function shouldSkipRequestPath(string $path): bool
    {
        foreach ($this->skipPaths as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldSkipRequest(mixed $request): bool
    {
        if ($this->skipRequest instanceof \Closure) {
            return ($this->skipRequest)($request);
        }

        return false;
    }

    protected function getRequestQuery(mixed $request): mixed
    {
        if (!$this->logRequestQuery) {
            return null;
        }
        if ($this->logRequestQueryFn instanceof \Closure) {
            $result = ($this->logRequestQueryFn)($request);
            if ($result === false) {
                return null;
            }
            if ($result !== null) {
                return $result;
            }
        }
        $result = match (true) {
            $request instanceof WebmanRequest => $request->queryString(),
            $request instanceof SymfonyRequest => $request->getQueryString(),
            default => null,
        };
        return $result ?: null;
    }

    private function getRequestBody(mixed $request): mixed
    {
        if (!$this->logRequestBody) {
            return null;
        }
        if ($this->logRequestBodyFn instanceof \Closure) {
            $result = ($this->logRequestBodyFn)($request);
            if ($result === false) {
                return null;
            }
            if ($result !== null) {
                return $result;
            }
        }
        [$contentType, $contentLength] = match (true) {
            $request instanceof WebmanRequest => [
                $request->header('content-type'),
                $request->header('content-length'),
            ],
            $request instanceof SymfonyRequest => [
                $request->headers->get('content-type'),
                $request->headers->get('content-length'),
            ],
            default => [null, null],
        };
        if (
            !in_array($contentType, ['application/json', 'application/x-www-form-urlencoded'])
            || !is_numeric($contentLength)
            || $contentLength <= 0
        ) {
            return null;
        }

        $content = match (true) {
            $request instanceof WebmanRequest => $request->rawBody(),
            $request instanceof SymfonyRequest => $request->getContent(),
            default => null,
        };
        if (!$content) {
            return null;
        }
        if ($contentLength > $this->logRequestBodyLimitSize) {
            $content = substr($content, 0, $this->logRequestBodyLimitSize) . '...';
        }
        foreach ($this->logRequestBodySensitive as $key) {
            if (str_contains($content, $key)) {
                if (str_starts_with($content, '{')) {
                    // json 格式的 {"password":"123"}
                    $key = preg_quote($key, '/');
                    $content = (string)preg_replace(
                        '/"(' . $key . ')"\s*:\s*".*?"/im',
                        '"$1":"***"',
                        $content
                    );
                } elseif (str_contains($content, '=')) {
                    // form 格式的 password=123
                    $key = preg_quote($key, '/');
                    $content = (string)preg_replace(
                        '/(' . $key . ')=.+?(&|$)/im',
                        '$1=***$2',
                        $content
                    );
                } else {
                    $content = "[Contain Sensitive {$key}]";
                }
            }
        }

        return $content;
    }
}
