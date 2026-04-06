<?php

declare(strict_types=1);

namespace App;

use App\Model\AdminUser;
use App\Repository\AdminUserRepository;

final class Auth
{
    private const SESSION_USER_ID = 'admin_user_id';
    private const SESSION_USERNAME = 'admin_username';

    public function __construct(
        private readonly AdminUserRepository $users
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return false;
        }

        $user = $this->users->findByUsername($username);
        if ($user === null || !$user->verifyPassword($password)) {
            return false;
        }

        $this->login($user);
        return true;
    }

    public function login(AdminUser $user): void
    {
        $_SESSION[self::SESSION_USER_ID] = $user->id;
        $_SESSION[self::SESSION_USERNAME] = $user->username;
        session_regenerate_id(true);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_ID], $_SESSION[self::SESSION_USERNAME]);
        session_regenerate_id(true);
    }

    public function check(): bool
    {
        return isset($_SESSION[self::SESSION_USER_ID]) && (int) $_SESSION[self::SESSION_USER_ID] > 0;
    }

    /** @return array{id: int, username: string}|null */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }

        return [
            'id' => (int) $_SESSION[self::SESSION_USER_ID],
            'username' => (string) $_SESSION[self::SESSION_USERNAME],
        ];
    }

    public function requireLogin(): void
    {
        if (!$this->check()) {
            header('Location: login.php', true, 302);
            exit;
        }
    }
}
