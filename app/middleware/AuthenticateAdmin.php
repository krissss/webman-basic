<?php

namespace app\middleware;

use app\enums\AdminStatusEnum;
use app\enums\common\AppModuleEnum;
use app\exception\UserSeeException;
use app\model\Admin;
use WebmanTech\Auth\Interfaces\IdentityInterface;
use WebmanTech\Auth\Middleware\Authentication;

class AuthenticateAdmin extends Authentication
{
    private AppModuleEnum $appModule = AppModuleEnum::Admin;

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
            'admin.login.view',
            'admin.login',
            'admin.layout',
            'admin.logout',
        ];
    }

    protected function checkIdentity(IdentityInterface $identity): ?\WebmanTech\CommonUtils\Response
    {
        if (!$identity instanceof Admin) {
            throw new \InvalidArgumentException();
        }

        if (!AdminStatusEnum::isEnabled($identity->status)) {
            throw new UserSeeException('用户状态异常', 403);
        }

        return null;
    }
}
