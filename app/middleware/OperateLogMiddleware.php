<?php

namespace app\middleware;

use support\facade\Logger;
use WebmanTech\Logger\Middleware\HttpRequestLogMiddleware;

class OperateLogMiddleware extends HttpRequestLogMiddleware
{
    public function __construct()
    {
        parent::__construct([
            'logMinTimeMS' => 0,
            'channel' => Logger::CHANNEL_OPERATE_LOG,
        ]);
    }
}
