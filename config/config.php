<?php

declare(strict_types=1);

/**
 * Скопируйте в config.local.php и подставьте свои значения (config.local.php в .gitignore при необходимости).
 */
$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    return require $local;
}

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'inclusive',
        'user' => 'root',
        'pass' => 'root',
        'charset' => 'utf8mb4',
    ],
];
