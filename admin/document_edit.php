<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Csrf;
use App\Database;
use App\DocumentInput;
use App\Model\Document;
use App\PdfUpload;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$auth->requireLogin();
$user = $auth->user();

$pdo = Database::connection($config['db']);
$repo = new DocumentRepository($pdo);
$catRepo = new CategoryRepository($pdo);
$sectionTitles = $catRepo->titlesBySlug();
if ($sectionTitles === []) {
    $sectionTitles = DocumentInput::SECTIONS;
}
$pdfDirAbs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pdf';

$editId = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

$existing = $editId > 0 ? $repo->findById($editId) : null;
if ($editId > 0 && $existing === null) {
    header('Location: documents.php', true, 302);
    exit;
}

$firstSlug = array_key_first($sectionTitles) ?? 'family';
$defaultSection = isset($_GET['section']) && is_string($_GET['section']) && isset($sectionTitles[$_GET['section']])
    ? $_GET['section']
    : $firstSlug;

$formSection = $existing ? $existing->section : $defaultSection;
$formTitle = $existing ? $existing->title : '';
$formPath = $existing ? $existing->pdfPath : '';
$formIcon = $existing ? $existing->iconClass : 'bi-file-earmark-text-fill';
$formSort = $existing ? $existing->sortOrder : 10;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!Csrf::validate(is_string($token) ? $token : null)) {
        $error = 'Сессия устарела. Обновите страницу.';
    } else {
        $action = isset($_POST['_action']) && is_string($_POST['_action']) ? $_POST['_action'] : '';
        if ($action === 'delete') {
            if ($existing !== null) {
                $sec = $existing->section;
                $repo->deleteById($existing->id);
                header('Location: documents.php?section=' . rawurlencode($sec) . '&deleted=1', true, 302);
                exit;
            }
            $error = 'Не удалось удалить запись.';
        } elseif ($action === 'save') {
            $formSection = isset($_POST['section']) && is_string($_POST['section']) ? $_POST['section'] : 'family';
            $formTitle = isset($_POST['title']) && is_string($_POST['title']) ? $_POST['title'] : '';
            $formPath = isset($_POST['pdf_path']) && is_string($_POST['pdf_path']) ? $_POST['pdf_path'] : '';
            $formIcon = isset($_POST['icon_class']) && is_string($_POST['icon_class']) ? $_POST['icon_class'] : '';
            $formSort = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

            $file = isset($_FILES['pdf_file']) && is_array($_FILES['pdf_file']) ? $_FILES['pdf_file'] : [];
            $upload = PdfUpload::tryStore($file, $pdfDirAbs);
            if ($upload['status'] === 'error') {
                $error = $upload['message'];
            } else {
                if ($upload['status'] === 'ok') {
                    $formPath = $upload['path'];
                }
                $v = DocumentInput::validate($formSection, $formTitle, $formPath, $formIcon, $formSort, $sectionTitles);
                if (!$v['ok']) {
                    $error = $v['error'];
                } elseif ($existing === null) {
                    $doc = new Document(null, $v['section'], $v['title'], $v['pdfPath'], $v['iconClass'], $v['sortOrder']);
                    $repo->insert($doc);
                    header('Location: documents.php?section=' . rawurlencode($v['section']) . '&saved=1', true, 302);
                    exit;
                } else {
                    $doc = new Document($existing->id, $v['section'], $v['title'], $v['pdfPath'], $v['iconClass'], $v['sortOrder']);
                    $repo->update($doc);
                    header('Location: documents.php?section=' . rawurlencode($v['section']) . '&saved=1', true, 302);
                    exit;
                }
            }
        }
    }
}

$csrf = Csrf::token();
$title = $existing ? 'Редактировать документ' : 'Новый документ';

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
<main class="container py-4" style="max-width: 640px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Панель</a></li>
            <li class="breadcrumb-item"><a href="documents.php?section=<?= htmlspecialchars($formSection, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Документы</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $existing ? 'Изменение' : 'Новый' ?></li>
        </ol>
    </nav>

    <h1 class="h4 mb-3"><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>

    <?php if ($error !== '') : ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" class="card shadow-sm mb-3" enctype="multipart/form-data">
        <div class="card-body">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <input type="hidden" name="_action" value="save">
            <?php if ($existing !== null) : ?>
                <input type="hidden" name="id" value="<?= (int) $existing->id ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="section">Раздел</label>
                <select class="form-select" id="section" name="section" required>
                    <?php foreach ($sectionTitles as $key => $label) : ?>
                        <option value="<?= htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" <?= $formSection === $key ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="title">Заголовок на сайте</label>
                <input class="form-control" type="text" id="title" name="title" required maxlength="500" value="<?= htmlspecialchars($formTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="pdf_file">PDF-файл</label>
                <input class="form-control" type="file" id="pdf_file" name="pdf_file" accept="application/pdf,.pdf">
                <div class="form-text">Нажмите «Обзор» / «Выберите файл» и укажите PDF — он сохранится в папку <code>pdf/</code> на сервере. До 25 МБ; при больших файлах проверьте <code>upload_max_filesize</code> в php.ini.</div>
            </div>
            <?php
            if ($formPath !== '') {
                $pathForHref = str_replace('\\', '/', $formPath);
                $pdfOpenHref = '../' . implode('/', array_map('rawurlencode', explode('/', $pathForHref)));
            }
            ?>
            <?php if ($formPath !== '') : ?>
                <div class="alert alert-light border py-2 small mb-3">
                    <span class="text-secondary">Сейчас в базе:</span>
                    <code class="user-select-all"><?= htmlspecialchars($formPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code>
                    <a class="ms-2" href="<?= htmlspecialchars($pdfOpenHref, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" target="_blank" rel="noopener">Открыть</a>
                </div>
            <?php endif; ?>
            <details class="mb-3 border rounded p-3 bg-white">
                <summary class="fw-medium user-select-none" style="cursor: pointer">Указать путь вручную (без загрузки)</summary>
                <p class="form-text small mt-2 mb-2">Если файл уже загружен на сервер в <code>pdf/</code>, можно ввести путь вместо выбора файла выше.</p>
                <label class="form-label small text-secondary mb-1" for="pdf_path">Путь от корня сайта</label>
                <input class="form-control font-monospace form-control-sm" type="text" id="pdf_path" name="pdf_path" value="<?= htmlspecialchars($formPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="pdf/документ.pdf" autocomplete="off">
            </details>
            <div class="mb-3">
                <label class="form-label" for="icon_class">Иконка (Bootstrap Icons)</label>
                <input class="form-control" type="text" id="icon_class" name="icon_class" value="<?= htmlspecialchars($formIcon, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="bi-list-check">
            </div>
            <div class="mb-0">
                <label class="form-label" for="sort_order">Порядок сортировки</label>
                <input class="form-control" type="number" id="sort_order" name="sort_order" min="0" max="99999" value="<?= (int) $formSort ?>">
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-between">
            <a class="btn btn-outline-secondary" href="documents.php?section=<?= htmlspecialchars($formSection, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Отмена</a>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>

    <?php if ($existing !== null) : ?>
        <form method="post" onsubmit="return confirm('Удалить этот документ?');">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <input type="hidden" name="_action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $existing->id ?>">
            <button type="submit" class="btn btn-outline-danger">Удалить документ</button>
        </form>
    <?php endif; ?>
</main>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/views/admin/layout.php';
