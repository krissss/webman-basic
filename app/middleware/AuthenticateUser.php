<?php

namespace app\middleware;

use app\enums\common\AppModuleEnum;
use app\enums\UserStatusEnum;
use app\exception\UserSeeException;
use app\model\User;
use WebmanTech\Auth\Interfaces\IdentityInterface;
use WebmanTech\Auth\Middleware\Authentication;
use WebmanTech\CommonUtils\Response;

class AuthenticateUser extends Authentication
{
    private AppModuleEnum $appModule = AppModuleEnum::User;

    public function __construct()
    {
        parent::__construct($this->appModule->guardName());
    }

    public function process(mixed $request, mixed $handler): mixed
    {
        $request->app = $this->appModule->value; // 修正多应用模块

        return parent::process($request, $handler);
    }

    protected function optionalRoutes(): array
    {
        return [
            'user.login.view',
            'user.login',
            'user.layout',
            'user.logout',
            'user.login.admin',
        ];
    }

    protected function checkIdentity(IdentityInterface $identity): ?Response
    {
        if (!$identity instanceof User) {
            throw new \InvalidArgumentException();
        }

        if (!UserStatusEnum::isEnabled($identity->status)) {
            throw new UserSeeException('用户状态异常', 403);
        }

        return null;
    }
}
