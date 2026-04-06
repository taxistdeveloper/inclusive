<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Auth;
use App\Database;
use App\Repository\AdminUserRepository;

$pdo = Database::connection($config['db']);
$auth = new Auth(new AdminUserRepository($pdo));
