<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Database;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$auth->requireLogin();
$user = $auth->user();

$pdo = Database::connection($config['db']);
$catRepo = new CategoryRepository($pdo);
$docRepo = new DocumentRepository($pdo);

$items = $catRepo->findAllOrdered();
$counts = [];
foreach ($items as $c) {
    $counts[$c->slug] = $docRepo->countBySection($c->slug);
}

$saved = isset($_GET['saved']) && $_GET['saved'] === '1';
$deleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';

$title = 'Категории экосистемы';
ob_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand mb-0 h1 fs-5 text-white text-decoration-none" href="index.php"><i class="bi bi-shield-lock me-2"></i>Админка</a>
        <div class="navbar-nav ms-auto flex-row gap-2 align-items-center">
            <span class="navbar-text text-white-50 small d-none d-sm-inline"><?= htmlspecialchars($user['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
            <a class="btn btn-outline-light btn-sm" href="../index.php" target="_blank">Сайт</a>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Выйти</a>
        </div>
    </div>
</nav>
<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Категории</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">Категории (разделы)</h1>
        <a class="btn btn-primary btn-sm" href="category_edit.php"><i class="bi bi-plus-lg me-1"></i>Новая категория</a>
    </div>
    <p class="text-secondary small mb-4">Разделы отображаются на главной странице и в списке документов. У каждого раздела свой ключ (slug) — его нельзя изменить после создания.</p>

    <?php if ($saved) : ?>
        <div class="alert alert-success py-2">Сохранено.</div>
    <?php endif; ?>
    <?php if ($deleted) : ?>
        <div class="alert alert-success py-2">Категория удалена.</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 4rem">#</th>
                        <th scope="col">Ключ</th>
                        <th scope="col">Название</th>
                        <th scope="col" style="width: 6rem">Порядок</th>
                        <th scope="col" style="width: 8rem">Документов</th>
                        <th scope="col" style="width: 8rem"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items === []) : ?>
                        <tr>
                            <td colspan="6" class="text-secondary py-4 text-center">Категорий нет. Импортируйте <code>sql/migration_categories.sql</code> или создайте первую категорию.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($items as $row) : ?>
                            <tr>
                                <td class="text-muted small"><?= (int) $row->id ?></td>
                                <td><code class="small"><?= htmlspecialchars($row->slug, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code></td>
                                <td><?= htmlspecialchars($row->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td><?= (int) $row->sortOrder ?></td>
                                <td><?= (int) ($counts[$row->slug] ?? 0) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-outline-primary btn-sm" href="category_edit.php?id=<?= (int) $row->id ?>">Изменить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/views/admin/layout.php';
