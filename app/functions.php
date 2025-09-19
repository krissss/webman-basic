<?php
/**
 * Here is your custom functions.
 */

use app\components\ResponseLayout;
use Illuminate\Contracts\Support\Arrayable;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis\Component as AmisComponent;

function _json_response(int $code, string $msg, $data = null, array $headers = []): Response
{
    return ResponseLayout::fromInfo($code, $msg, $data, $headers)
        ->useToArrayForData(true)
        ->useStatusCode(false)
        ->toJsonResponse();
}

function json_success($data, string $msg = 'ok', int $code = 200, array $headers = []): Response
{
    return _json_response($code, $msg, $data, $headers);
}

function json_error(string $msg, int $code = 422, $data = null, array $headers = []): Response
{
    return _json_response($code, $msg, $data, $headers);
}

function admin_response($data, string $msg = '', array $extraInfo = []): Response
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
 * admin 重定向.
 *
 * @param string $target _self/_blank
 *
 * @return Response
 *
 * @see /public/js/amis-admin.js
 */
function admin_redirect(string $redirectUrl, string $msg = '', string $target = '_self')
{
    return admin_response([
        'redirect' => $redirectUrl,
        'target' => $target,
    ], $msg, ['status' => 301]);
}
