<?php
/**
 * Here is your custom functions.
 */

use app\components\ResponseLayout;
use Illuminate\Contracts\Support\Arrayable;
use WebmanTech\AmisAdmin\Amis\Component as AmisComponent;

function json_success($data, string $msg = 'ok', int $code = 200)
{
    return (new ResponseLayout(
        code: $code,
        msg: $msg,
        data: $data,
    ))->toResponse();
}

function json_error(string $msg, int $code = 422, $data = null)
{
    return (new ResponseLayout(
        code: $code,
        msg: $msg,
        data: $data,
    ))->toResponse();
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
 * admin 重定向.
 *
 * @param string $target _self/_blank
 *
 * @return \Webman\Http\Response
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
