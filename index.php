<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

use App\Database;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$pdo = Database::connection($config['db']);
$repo = new DocumentRepository($pdo);
$catRepo = new CategoryRepository($pdo);
$categories = $catRepo->findAllOrdered();
$documentsBySlug = [];
foreach ($categories as $cat) {
    $documentsBySlug[$cat->slug] = $repo->findBySection($cat->slug);
}

require __DIR__ . '/views/public/home.php';
