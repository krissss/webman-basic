<?php

namespace app\enums\common;

enum AppModuleEnum: string
{
    case Admin = 'admin';
    case User = 'user';
    case Api = 'api';

    public function guardName(): string
    {
        // 见 config/plugin/webman-tech/auth/auth.php 下配置的 guards key
        return match ($this) {
            self::Admin => 'admin',
            self::User => 'user',
            self::Api => 'api_user',
        };
    }
}
