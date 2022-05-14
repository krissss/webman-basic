<?php

namespace App\components;

class Security
{
    /**
     * 密码hash
     * @param string $password
     * @return string
     */
    public function generatePasswordHash(string $password): string
    {
        return strtr(substr(base64_encode(md5($password)), 0, 32), '+/', '_-');
    }

    /**
     * 验证密码
     * @param string $password
     * @param string $passwordHash
     * @return string
     */
    public function validatePassword(string $password, string $passwordHash): string
    {
        return $this::generatePasswordHash($password) === $passwordHash;
    }
}
