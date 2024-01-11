<?php

$enable = config('app.debug', false) && class_exists('WebmanTech\Debugbar\WebmanDebugBar');

return [
    'enable' => $enable,
    /**
     * @see \WebmanTech\Debugbar\WebmanDebugBar::$config
     */
    'debugbar' => [
        'enable' => $enable,
        'skip_request_path' => [
            '/admin/log-reader*',
        ],
        'open_handler_url_make' => fn (string $url) => request() ? route_url($url) : $url,
    ],
];
