<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Csrf;
use App\Database;
use App\DocumentInput;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$auth->requireLogin();
$user = $auth->user();

$pdo = Database::connection($config['db']);
$catRepo = new CategoryRepository($pdo);
$sectionTitles = $catRepo->titlesBySlug();
if ($sectionTitles === []) {
    $sectionTitles = DocumentInput::SECTIONS;
}
$firstSlug = array_key_first($sectionTitles) ?? 'family';
$section = isset($_GET['section']) && is_string($_GET['section']) ? $_GET['section'] : $firstSlug;
if (!isset($sectionTitles[$section])) {
    $section = $firstSlug;
}

$repo = new DocumentRepository($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_action']) && $_POST['_action'] === 'delete') {
    $token = $_POST['_csrf'] ?? '';
    if (!Csrf::validate(is_string($token) ? $token : null)) {
        header('Location: documents.php?section=' . rawurlencode($section) . '&err=csrf', true, 302);
        exit;
    }
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($id > 0) {
        $repo->deleteById($id);
    }
    header('Location: documents.php?section=' . rawurlencode($section) . '&deleted=1', true, 302);
    exit;
}

$items = $repo->findBySection($section);
$csrf = Csrf::token();
$title = 'Документы';
$err = isset($_GET['err']) && $_GET['err'] === 'csrf' ? 'Сессия устарела. Повторите действие.' : '';
$deleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';
$saved = isset($_GET['saved']) && $_GET['saved'] === '1';

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
            <li class="breadcrumb-item active" aria-current="page">Документы</li>
        </ol>
    </nav>

    <h1 class="h4 mb-3">PDF в модальных окнах</h1>
    <p class="text-secondary small mb-3">Редактируйте PDF по разделам. Разделы задаются в <a href="categories.php">категориях</a>.</p>

    <?php if ($err !== '') : ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($err, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($saved) : ?>
        <div class="alert alert-success py-2">Изменения сохранены.</div>
    <?php endif; ?>
    <?php if ($deleted) : ?>
        <div class="alert alert-success py-2">Запись удалена.</div>
    <?php endif; ?>

    <ul class="nav nav-pills mb-4 flex-wrap gap-1">
        <?php foreach ($sectionTitles as $key => $label) : ?>
            <li class="nav-item">
                <a class="nav-link <?= $section === $key ? 'active' : '' ?>" href="documents.php?section=<?= htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-secondary small"><?= count($items) ?> <?= count($items) === 1 ? 'документ' : 'документов' ?></span>
        <a class="btn btn-primary btn-sm" href="document_edit.php?section=<?= htmlspecialchars($section, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><i class="bi bi-plus-lg me-1"></i>Добавить</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 4rem">#</th>
                        <th scope="col">Заголовок</th>
                        <th scope="col">Путь к PDF</th>
                        <th scope="col" style="width: 8rem">Порядок</th>
                        <th scope="col" style="width: 8rem"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items === []) : ?>
                        <tr>
                            <td colspan="5" class="text-secondary py-4 text-center">Пока нет документов. Нажмите «Добавить».</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($items as $row) : ?>
                            <tr>
                                <td class="text-muted small"><?= (int) $row->id ?></td>
                                <td><?= htmlspecialchars($row->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td><code class="small"><?= htmlspecialchars($row->pdfPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code></td>
                                <td><?= (int) $row->sortOrder ?></td>
                                <td class="text-end">
                                    <a class="btn btn-outline-primary btn-sm" href="document_edit.php?id=<?= (int) $row->id ?>">Изменить</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Удалить эту запись?');">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                        <input type="hidden" name="_action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $row->id ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                                    </form>
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
