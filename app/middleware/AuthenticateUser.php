<?php

namespace app\middleware;

use Kriss\WebmanAuth\Interfaces\GuardInterface;
use Kriss\WebmanAuth\Middleware\Authentication;
use support\facade\Auth;

class AuthenticateUser extends Authentication
{
    /**
     * @inheritDoc
     */
    public function getGuard(): GuardInterface
    {
        return Auth::guardUser();
    }

    /**
     * @inheritDoc
     */
    protected function optionalRoutes(): array
    {
        return [];
    }
}
