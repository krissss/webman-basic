<?php

use Illuminate\Contracts\Container\Container as ContainerContract;
use support\facade\Container;

return [
    /**
     * @see \WebmanTech\LaravelConsole\Kernel::$config
     */
    'container' => function (): ContainerContract {
        return Container::instance();
    },
    'commands' => [
        // commandName
    ],
];
