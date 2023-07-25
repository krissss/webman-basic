<?php

namespace app\components\interfaces;

interface SecurityInterface
{
    /**
     * 密码hash.
     */
    public function generatePasswordHash(string $password): string;

    /**
     * 验证密码
     */
    public function validatePassword(string $password, string $passwordHash): bool;

    /**
     * 生成随机字符串.
     */
    public function generateRandomString(int $length = 16): string;
}
