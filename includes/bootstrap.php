<?php

declare(strict_types=1);

session_start();

$config = require dirname(__DIR__) . '/config/config.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/App/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, $len)) . '.php';
    $file = $baseDir . $relative;
    if (is_file($file)) {
        require $file;
    }
});
