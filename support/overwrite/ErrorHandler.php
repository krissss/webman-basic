<?php

namespace support\overwrite;

use Throwable;
use Tinywan\ExceptionHandler\Event\DingTalkRobotEvent;
use Tinywan\ExceptionHandler\Exception\BaseException;
use Tinywan\ExceptionHandler\Handler;
use Webman\Http\Request;
use Webman\Http\Response;

class ErrorHandler extends Handler
{
    /**
     * @var array
     */
    protected array $exceptionInfo = [
        'statusCode' => 0,
        'responseHeader' => [],
        'errorCode' => 0,
        'errorMsg' => '',
    ];
    /**
     * @var array
     */
    protected array $responseData = [];
    /**
     * @var array
     */
    protected array $config = [];

    /**
     * 重写该方法的逻辑
     * @inheritDoc
     */
    public function render(Request $request, Throwable $e): Response
    {
        $this->config = array_merge($this->config, config('plugin.tinywan.exception-handler.app.exception_handler', []));

        $this->addRequestInfoToResponse($request);
        $this->solveAllException($e);
        $this->addDebugInfoToResponse($e);
        $this->triggerNotifyEvent($e);
        $this->triggerARMSTrace($e);

        return $this->buildResponse();
    }

    /**
     * 请求的相关信息
     * @param Request $request
     * @return void
     */
    protected function addRequestInfoToResponse(Request $request): void
    {
        $this->responseData = array_merge($this->responseData, [
            'request_url' => $request->method() . ' ' . $request->fullUrl(),
            'timestamp' => date('Y-m-d H:i:s'),
            'client_ip' => $request->getRealIp(),
            'request_param' => $request->all(),
        ]);
    }

    /**
     * 处理异常数据
     * @param Throwable $e
     */
    protected function solveAllException(Throwable $e)
    {
        // 处理常用的 http 异常
        if ($e instanceof BaseException) {
            $this->exceptionInfo['statusCode'] = $e->statusCode;
            $this->exceptionInfo['responseHeader'] = $e->header;
            $this->exceptionInfo['errorMsg'] = $e->errorMessage;
            $this->exceptionInfo['errorCode'] = $e->errorCode;
            if ($e->data) {
                $this->responseData = array_merge($this->responseData, $e->data);
            }
            return;
        }
        // 处理扩展的其他异常
        $this->solveExtraException($e);
    }

    /**
     * 处理扩展的异常
     * @param Throwable $e
     */
    protected function solveExtraException(Throwable $e): void
    {
        $status = $this->config['status'];

        $this->exceptionInfo['errorMsg'] = $e->getMessage();
        if ($e instanceof \Tinywan\Validate\Exception\ValidateException) {
            $this->exceptionInfo['statusCode'] = $status['validate'];
        } elseif ($e instanceof \Tinywan\Jwt\Exception\JwtTokenException) {
            $this->exceptionInfo['statusCode'] = $status['jwt_token'];
        } elseif ($e instanceof \Tinywan\Jwt\Exception\JwtTokenExpiredException) {
            $this->exceptionInfo['statusCode'] = $status['jwt_token_expired'];
        } elseif ($e instanceof \InvalidArgumentException) {
            $this->exceptionInfo['statusCode'] = $status['invalid_argument'] ?? 415;
            $this->exceptionInfo['errorMsg'] = '预期参数配置异常：' . $e->getMessage();
        } else {
            $this->exceptionInfo['statusCode'] = $status['server_error'];
            $this->exceptionInfo['errorCode'] = 50000;
        }
    }

    /**
     * 添加 debug 信息到 response
     * @param Throwable $e
     * @return void
     */
    protected function addDebugInfoToResponse(Throwable $e): void
    {
        if (config('app.debug')) {
            $this->responseData['error_message'] = $this->exceptionInfo['errorMsg'];
            $this->responseData['error_trace'] = explode("\n", $e->getTraceAsString());
        }
    }

    /**
     * 触发通知事件
     * @param Throwable $e
     * @return void
     */
    protected function triggerNotifyEvent(Throwable $e): void
    {
        if ($this->config['event']['enable'] ?? false) {
            $responseData['message'] = $this->exceptionInfo['errorMsg'];
            $responseData['file'] = $e->getFile();
            $responseData['line'] = $e->getLine();
            DingTalkRobotEvent::dingTalkRobot($responseData);
        }
    }

    /**
     * 触发 arms trace
     * @param Throwable $e
     * @return void
     */
    protected function triggerARMSTrace(Throwable $e): void
    {
        if (isset(request()->tracer) && isset(request()->rootSpan)) {
            $samplingFlags = request()->rootSpan->getContext();
            $this->exceptionInfo['header']['Trace-Id'] = $samplingFlags->getTraceId();
            $exceptionSpan = request()->tracer->newChild($samplingFlags);
            $exceptionSpan->setName('exception');
            $exceptionSpan->start();
            $exceptionSpan->tag('error.code', (string)$this->exceptionInfo['errorCode']);
            $value = [
                'event' => 'error',
                'message' => $this->exceptionInfo['errorMsg'],
                'stack' => 'Exception:' . $e->getFile() . '|' . $e->getLine(),
            ];
            $exceptionSpan->annotate(json_encode($value));
            $exceptionSpan->finish();
        }
    }

    /**
     * 构造 Response
     * @return Response
     */
    protected function buildResponse(): Response
    {
        $bodyKey = array_keys($this->config['body']);
        $responseBody = [
            $bodyKey[0] ?? 'code' => $this->exceptionInfo['errorCode'],
            $bodyKey[1] ?? 'msg' => $this->exceptionInfo['errorMsg'],
            $bodyKey[2] ?? 'data' => $this->responseData,
        ];

        $header = array_merge(['Content-Type' => 'application/json;charset=utf-8'], $this->exceptionInfo['responseHeader']);
        return new Response($this->exceptionInfo['statusCode'], $header, json_encode($responseBody));
    }
}
