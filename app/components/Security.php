<?php

namespace app\components;

use app\components\interfaces\SecurityInterface;
use Illuminate\Support\Str;

class Security implements SecurityInterface
{
    /**
     * {@inheritDoc}
     */
    public function generatePasswordHash(string $password): string
    {
        return strtr(substr(base64_encode(md5($password)), 0, 32), '+/', '_-');
    }

    /**
     * {@inheritDoc}
     */
    public function validatePassword(string $password, string $passwordHash): bool
    {
        return $this::generatePasswordHash($password) === $passwordHash;
    }

    /**
     * {@inheritDoc}
     */
    public function generateRandomString(int $length = 16): string
    {
        return Str::random($length);
    }
}
