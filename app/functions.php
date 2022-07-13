<?php
/**
 * Here is your custom functions.
 */

use Illuminate\Contracts\Support\Arrayable;
use Kriss\WebmanAmisAdmin\Amis\Component as AmisComponent;
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

function admin_response($data, string $msg = '', array $extraInfo = [])
{
    if ($data instanceof Arrayable) {
        $data = $data->toArray();
    }
    if ($data instanceof AmisComponent) {
        $data = $data->toArray();
    }
    if (is_string($data)) {
        $data = ['result' => $data];
    }
    return amis_response($data, $msg, $extraInfo);
}

/**
 * admin 重定向
 * @see /public/js/amis-admin.js
 * @param string $redirectUrl
 * @param string $msg
 * @return \Webman\Http\Response
 */
function admin_redirect(string $redirectUrl, string $msg = '')
{
    return admin_response(['redirect' => $redirectUrl], $msg, ['status' => 301]);
}
