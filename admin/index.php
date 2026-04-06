<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Database;
use App\DocumentInput;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$auth->requireLogin();
$user = $auth->user();

$pdo = Database::connection($config['db']);
$docRepo = new DocumentRepository($pdo);
$catRepo = new CategoryRepository($pdo);
$categoryList = $catRepo->findAllOrdered();

$counts = [];
foreach ($categoryList as $cat) {
    $counts[$cat->slug] = $docRepo->countBySection($cat->slug);
}
if ($categoryList === []) {
    foreach (array_keys(DocumentInput::SECTIONS) as $key) {
        $counts[$key] = $docRepo->countBySection($key);
    }
}

$title = 'Панель управления';
ob_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fs-5"><i class="bi bi-shield-lock me-2"></i>Админка</span>
        <div class="navbar-nav ms-auto flex-row gap-2 align-items-center">
            <span class="navbar-text text-white-50 small d-none d-sm-inline"><?= htmlspecialchars($user['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
            <a class="btn btn-outline-light btn-sm" href="../index.php" target="_blank">Открыть сайт</a>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Выйти</a>
        </div>
    </div>
</nav>
<main class="container py-4">
    <h1 class="h4 mb-4">Панель управления</h1>
    <p class="text-secondary mb-3">PDF-документы в модальных окнах блоков экосистемы на главной. Разделы настраиваются в <a href="categories.php">категориях</a>.</p>
    <p class="mb-4"><a class="btn btn-outline-primary btn-sm" href="categories.php"><i class="bi bi-folder-plus me-1"></i>Категории экосистемы</a></p>

    <div class="row g-3">
        <?php
        if ($categoryList !== []) {
            foreach ($categoryList as $cat) :
                $sec = $cat->slug;
                $label = $cat->title;
                $n = (int) ($counts[$sec] ?? 0);
                $borderClass = $cat->adminBorderClass !== '' ? 'border ' . $cat->adminBorderClass : 'border';
                $cardStyle = $cat->adminBorderStyle ?? '';
                $iwStyle = $cat->adminIconWrapStyle ?? '';
                ?>
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100 <?= htmlspecialchars($borderClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="<?= htmlspecialchars($cardStyle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-3 <?= htmlspecialchars($cat->adminIconWrapClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="<?= htmlspecialchars($iwStyle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                            <i class="bi <?= htmlspecialchars($cat->iconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> fs-3"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h2 class="h6 card-title"><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
                            <p class="mb-3 small"><span class="badge bg-secondary"><?= $n ?></span> документов</p>
                            <a class="btn btn-primary btn-sm" href="documents.php?section=<?= htmlspecialchars($sec, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Редактировать</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endforeach;
        } else {
            foreach (DocumentInput::SECTIONS as $sec => $label) :
                $n = (int) ($counts[$sec] ?? 0);
                ?>
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100 border border-secondary border-opacity-25">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-3 bg-secondary bg-opacity-10 text-secondary">
                            <i class="bi bi-folder2-open fs-3"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h2 class="h6 card-title"><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
                            <p class="mb-3 small"><span class="badge bg-secondary"><?= $n ?></span> документов</p>
                            <a class="btn btn-primary btn-sm" href="documents.php?section=<?= htmlspecialchars($sec, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Редактировать</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endforeach;
        }
        ?>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h6 card-title">База данных</h2>
            <p class="text-secondary small mb-0">Таблицы <code>categories</code> (разделы) и <code>documents</code> (поле <code>section</code> = ключ раздела). Новая установка: <code>sql/schema.sql</code>. Для уже существующей БД выполните <code>sql/migration_categories.sql</code>.</p>
        </div>
    </div>
</main>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/views/admin/layout.php';
