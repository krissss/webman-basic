<?php

namespace app\middleware;

use WebmanTech\AmisAdmin\Middleware\AmisModuleChangeMiddleware;

class AmisModuleChange2User extends AmisModuleChangeMiddleware
{
    public function __construct()
    {
        parent::__construct('amis-user');
    }
}
