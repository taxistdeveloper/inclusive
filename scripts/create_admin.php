#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Создание администратора (один раз после импорта schema.sql).
 *
 * Пример: php scripts/create_admin.php admin ВашСложныйПароль
 */

if (PHP_SAPI !== 'cli') {
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=utf-8', true, 403);
    }
    echo "Только CLI. Запуск из корня проекта:\nphp scripts/create_admin.php <логин> <пароль>\n";
    exit(1);
}

if ($argc < 3) {
    fwrite(STDERR, "Использование: php scripts/create_admin.php <логин> <пароль>\n");
    exit(1);
}

$username = trim((string) $argv[1]);
$password = (string) $argv[2];

if ($username === '' || strlen($password) < 8) {
    fwrite(STDERR, "Логин не пустой, пароль не короче 8 символов.\n");
    exit(1);
}

require dirname(__DIR__) . '/includes/bootstrap.php';

use App\Database;

$pdo = Database::connection($config['db']);
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);

try {
    $stmt->execute(['u' => $username, 'p' => $hash]);
} catch (Throwable $e) {
    fwrite(STDERR, 'Ошибка БД: ' . $e->getMessage() . "\n");
    exit(1);
}

echo "Готово: пользователь «{$username}» создан или пароль обновлён.\n";
