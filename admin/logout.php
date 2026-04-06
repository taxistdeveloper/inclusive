<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

$auth->logout();
header('Location: login.php', true, 302);
exit;
