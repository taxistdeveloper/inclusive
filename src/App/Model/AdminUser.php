<?php

declare(strict_types=1);

namespace App\Model;

final class AdminUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        private readonly string $passwordHash
    ) {
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->passwordHash);
    }
}
