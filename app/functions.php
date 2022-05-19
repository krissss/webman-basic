<?php
/**
 * Here is your custom functions.
 */

function json_success($data, string $msg = 'ok', int $code = 200)
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}

function json_error(string $msg, int $code = 422, $data = null)
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}
