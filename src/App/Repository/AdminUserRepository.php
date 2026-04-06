<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\AdminUser;
use PDO;

final class AdminUserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByUsername(string $username): ?AdminUser
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, password_hash FROM admin_users WHERE username = :u LIMIT 1'
        );
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return new AdminUser(
            (int) $row['id'],
            (string) $row['username'],
            (string) $row['password_hash']
        );
    }
}
