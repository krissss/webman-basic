<?php

namespace app\middleware;

use Kriss\WebmanAuth\Middleware\Authentication;

class AuthenticateAdmin extends Authentication
{
    /**
     * @inheritDoc
     */
    protected function optionalRoutes(): array
    {
        return [
            '/admin/auth/logout',
        ];
    }
}
