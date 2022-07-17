<?php

$enable = config('app.debug', false) && class_exists('Kriss\WebmanDebugBar\WebmanDebugBar');

return [
    'enable' => $enable,
    /**
     * @see \Kriss\WebmanDebugBar\WebmanDebugBar::$config
     */
    'debugbar' => [
        'enable' => $enable,
    ],
];
