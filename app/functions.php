<?php
/**
 * Here is your custom functions.
 */

use support\Response;
use Yiisoft\Json\Json;

function _json_response($data)
{
    return new Response(200, ['Content-Type' => 'application/json'], Json::encode($data));
}

function json_success($data, string $msg = 'ok', int $code = 200)
{
    return _json_response([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}

function json_error(string $msg, int $code = 422, $data = null)
{
    return _json_response([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}
