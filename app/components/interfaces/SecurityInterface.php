<?php

namespace app\components\interfaces;

interface SecurityInterface
{
    /**
     * 密码hash
     * @param string $password
     * @return string
     */
    public function generatePasswordHash(string $password): string;

    /**
     * 验证密码
     * @param string $password
     * @param string $passwordHash
     * @return bool
     */
    public function validatePassword(string $password, string $passwordHash): bool;

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public function generateRandomString(int $length = 16): string;
}
