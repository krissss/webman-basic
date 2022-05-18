<?php

namespace app\components;

use app\components\interfaces\SecurityInterface;

class Security implements SecurityInterface
{
    /**
     * @inheritDoc
     */
    public function generatePasswordHash(string $password): string
    {
        return strtr(substr(base64_encode(md5($password)), 0, 32), '+/', '_-');
    }

    /**
     * @inheritDoc
     */
    public function validatePassword(string $password, string $passwordHash): bool
    {
        return $this::generatePasswordHash($password) === $passwordHash;
    }
}
