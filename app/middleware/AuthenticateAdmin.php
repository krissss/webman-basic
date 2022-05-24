<?php

namespace app\middleware;

use Kriss\WebmanAuth\Interfaces\GuardInterface;
use Kriss\WebmanAuth\Middleware\Authentication;
use support\facade\Auth;

class AuthenticateAdmin extends Authentication
{
    /**
     * @inheritDoc
     */
    public function getGuard(): GuardInterface
    {
        return Auth::guardAdmin();
    }

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
