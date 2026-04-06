<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\CategoryInput;
use App\Csrf;
use App\Database;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

$auth->requireLogin();
$user = $auth->user();

$pdo = Database::connection($config['db']);
$catRepo = new CategoryRepository($pdo);
$docRepo = new DocumentRepository($pdo);

$editId = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

$existing = $editId > 0 ? $catRepo->findById($editId) : null;
if ($editId > 0 && $existing === null) {
    header('Location: categories.php', true, 302);
    exit;
}

$defaults = static function (): array {
    return [
        'slug' => '',
        'title' => '',
        'subtitle' => '',
        'icon_class' => 'bi-circle-fill',
        'pill_label' => 'Материалы',
        'badge_class' => 'bg-primary',
        'badge_style' => '',
        'alert_strong' => 'Важно:',
        'alert_text' => '',
        'modal_id' => '',
        'sort_order' => 10,
        'diagram_icon_class' => 'text-primary',
        'admin_icon_wrap_class' => 'bg-primary bg-opacity-10 text-primary',
        'admin_border_class' => 'border border-primary border-opacity-25',
        'admin_icon_wrap_style' => '',
        'admin_border_style' => '',
    ];
};

$form = $defaults();
if ($existing !== null) {
    $form['slug'] = $existing->slug;
    $form['title'] = $existing->title;
    $form['subtitle'] = $existing->subtitle;
    $form['icon_class'] = $existing->iconClass;
    $form['pill_label'] = $existing->pillLabel;
    $form['badge_class'] = $existing->badgeClass;
    $form['badge_style'] = $existing->badgeStyle ?? '';
    $form['alert_strong'] = $existing->alertStrong;
    $form['alert_text'] = $existing->alertText;
    $form['modal_id'] = $existing->modalId;
    $form['sort_order'] = $existing->sortOrder;
    $form['diagram_icon_class'] = $existing->diagramIconClass;
    $form['admin_icon_wrap_class'] = $existing->adminIconWrapClass;
    $form['admin_border_class'] = $existing->adminBorderClass;
    $form['admin_icon_wrap_style'] = $existing->adminIconWrapStyle ?? '';
    $form['admin_border_style'] = $existing->adminBorderStyle ?? '';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!Csrf::validate(is_string($token) ? $token : null)) {
        $error = 'Сессия устарела. Обновите страницу.';
    } else {
        $action = isset($_POST['_action']) && is_string($_POST['_action']) ? $_POST['_action'] : '';
        if ($action === 'delete') {
            if ($existing !== null) {
                if ($docRepo->countBySection($existing->slug) > 0) {
                    $error = 'Нельзя удалить раздел, пока в нём есть документы. Перенесите или удалите их.';
                } else {
                    $catRepo->deleteById($existing->id);
                    header('Location: categories.php?deleted=1', true, 302);
                    exit;
                }
            } else {
                $error = 'Не удалось удалить запись.';
            }
        } elseif ($action === 'save') {
            $postStr = static function (string $k): string {
                $v = $_POST[$k] ?? '';

                return is_string($v) ? $v : '';
            };
            $postInt = static function (string $k): int {
                return isset($_POST[$k]) ? (int) $_POST[$k] : 0;
            };

            $form['slug'] = $existing !== null ? $existing->slug : $postStr('slug');
            $form['title'] = $postStr('title');
            $form['subtitle'] = $postStr('subtitle');
            $form['icon_class'] = $postStr('icon_class');
            $form['pill_label'] = $postStr('pill_label');
            $form['badge_class'] = $postStr('badge_class');
            $form['badge_style'] = $postStr('badge_style');
            $form['alert_strong'] = $postStr('alert_strong');
            $form['alert_text'] = $postStr('alert_text');
            $form['modal_id'] = $postStr('modal_id');
            $form['sort_order'] = $postInt('sort_order');
            $form['diagram_icon_class'] = $postStr('diagram_icon_class');
            $form['admin_icon_wrap_class'] = $postStr('admin_icon_wrap_class');
            $form['admin_border_class'] = $postStr('admin_border_class');
            $form['admin_icon_wrap_style'] = $postStr('admin_icon_wrap_style');
            $form['admin_border_style'] = $postStr('admin_border_style');

            if ($existing === null) {
                $v = CategoryInput::validateNew(
                    $form['slug'],
                    $form['title'],
                    $form['subtitle'],
                    $form['icon_class'],
                    $form['pill_label'],
                    $form['badge_class'],
                    $form['badge_style'],
                    $form['alert_strong'],
                    $form['alert_text'],
                    $form['modal_id'],
                    $form['sort_order'],
                    $form['admin_icon_wrap_class'],
                    $form['admin_border_class'],
                    $form['admin_icon_wrap_style'],
                    $form['admin_border_style'],
                    $form['diagram_icon_class'],
                );
                if (!$v['ok']) {
                    $error = $v['error'];
                } elseif ($catRepo->slugExists($v['category']->slug)) {
                    $error = 'Раздел с таким ключом уже существует.';
                } elseif ($catRepo->modalIdExists($v['category']->modalId)) {
                    $error = 'Модальное окно с таким ID уже используется.';
                } else {
                    $catRepo->insert($v['category']);
                    header('Location: categories.php?saved=1', true, 302);
                    exit;
                }
            } else {
                $v = CategoryInput::validateExisting(
                    $existing->id,
                    $existing->slug,
                    $form['title'],
                    $form['subtitle'],
                    $form['icon_class'],
                    $form['pill_label'],
                    $form['badge_class'],
                    $form['badge_style'],
                    $form['alert_strong'],
                    $form['alert_text'],
                    $form['modal_id'],
                    $form['sort_order'],
                    $form['admin_icon_wrap_class'],
                    $form['admin_border_class'],
                    $form['admin_icon_wrap_style'],
                    $form['admin_border_style'],
                    $form['diagram_icon_class'],
                );
                if (!$v['ok']) {
                    $error = $v['error'];
                } elseif ($catRepo->modalIdExists($v['category']->modalId, $existing->id)) {
                    $error = 'Модальное окно с таким ID уже используется.';
                } else {
                    $catRepo->update($v['category']);
                    header('Location: categories.php?saved=1', true, 302);
                    exit;
                }
            }
        }
    }
}

$csrf = Csrf::token();
$title = $existing ? 'Редактировать категорию' : 'Новая категория';

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
<main class="container py-4" style="max-width: 720px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Панель</a></li>
            <li class="breadcrumb-item"><a href="categories.php">Категории</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $existing ? 'Изменение' : 'Новая' ?></li>
        </ol>
    </nav>

    <h1 class="h4 mb-3"><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>

    <?php if ($error !== '') : ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" class="card shadow-sm mb-3">
        <div class="card-body">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <input type="hidden" name="_action" value="save">
            <?php if ($existing !== null) : ?>
                <input type="hidden" name="id" value="<?= (int) $existing->id ?>">
            <?php endif; ?>

            <?php if ($existing === null) : ?>
                <div class="mb-3">
                    <label class="form-label" for="slug">Ключ раздела (slug)</label>
                    <input class="form-control font-monospace" type="text" id="slug" name="slug" required maxlength="32" value="<?= htmlspecialchars($form['slug'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" pattern="[a-z][a-z0-9_]{1,29}" title="Латиница, цифры и _, от 2 до 30 символов" placeholder="moy_razdel">
                    <div class="form-text">Латинские буквы в нижнем регистре, цифры и символ _. Потом используется в базе для привязки документов — изменить нельзя.</div>
                </div>
            <?php else : ?>
                <div class="mb-3">
                    <span class="form-label d-block">Ключ раздела</span>
                    <code class="user-select-all"><?= htmlspecialchars($existing->slug, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="title">Название на сайте</label>
                <input class="form-control" type="text" id="title" name="title" required maxlength="255" value="<?= htmlspecialchars($form['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="subtitle">Краткое описание под названием</label>
                <input class="form-control" type="text" id="subtitle" name="subtitle" maxlength="500" value="<?= htmlspecialchars($form['subtitle'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="icon_class">Иконка (Bootstrap Icons)</label>
                    <input class="form-control" type="text" id="icon_class" name="icon_class" value="<?= htmlspecialchars($form['icon_class'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="bi-globe">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="diagram_icon_class">Цвет иконки на главной</label>
                    <input class="form-control" type="text" id="diagram_icon_class" name="diagram_icon_class" maxlength="64" value="<?= htmlspecialchars($form['diagram_icon_class'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="text-primary">
                    <div class="form-text small">Класс Bootstrap, например <code>text-primary</code>, <code>text-danger</code>.</div>
                </div>
            </div>

            <hr class="my-4">
            <h2 class="h6">Модальное окно со списком PDF</h2>
            <div class="mb-3">
                <label class="form-label" for="modal_id">HTML id модального окна</label>
                <input class="form-control font-monospace" type="text" id="modal_id" name="modal_id" required maxlength="64" value="<?= htmlspecialchars($form['modal_id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="moyModal">
                <div class="form-text">Должен быть уникальным. Обычно <code>имяРазделаModal</code>.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="pill_label">Подпись над списком документов</label>
                <input class="form-control" type="text" id="pill_label" name="pill_label" maxlength="128" value="<?= htmlspecialchars($form['pill_label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="badge_class">Класс бейджа в заголовке модалки</label>
                    <input class="form-control" type="text" id="badge_class" name="badge_class" maxlength="128" value="<?= htmlspecialchars($form['badge_class'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="bg-primary">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="badge_style">Стиль бейджа (необязательно)</label>
                    <input class="form-control" type="text" id="badge_style" name="badge_style" maxlength="255" value="<?= htmlspecialchars($form['badge_style'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="background-color: #7209b7;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="alert_strong">Жирная часть подсказки внизу</label>
                <input class="form-control" type="text" id="alert_strong" name="alert_strong" maxlength="128" value="<?= htmlspecialchars($form['alert_strong'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="alert_text">Текст подсказки</label>
                <textarea class="form-control" id="alert_text" name="alert_text" rows="2" maxlength="1000"><?= htmlspecialchars($form['alert_text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
            </div>
            <div class="mb-0">
                <label class="form-label" for="sort_order">Порядок на главной и в меню</label>
                <input class="form-control" type="number" id="sort_order" name="sort_order" min="0" max="99999" value="<?= (int) $form['sort_order'] ?>">
            </div>

            <details class="mt-4 border rounded p-3 bg-white">
                <summary class="fw-medium user-select-none" style="cursor: pointer">Оформление карточки в админ-панели</summary>
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1" for="admin_icon_wrap_class">Классы обёртки иконки</label>
                        <input class="form-control form-control-sm" type="text" id="admin_icon_wrap_class" name="admin_icon_wrap_class" maxlength="255" value="<?= htmlspecialchars($form['admin_icon_wrap_class'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1" for="admin_border_class">Классы рамки карточки</label>
                        <input class="form-control form-control-sm" type="text" id="admin_border_class" name="admin_border_class" maxlength="255" value="<?= htmlspecialchars($form['admin_border_class'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <div class="form-text small">Пусто = только класс <code>border</code>.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary mb-1" for="admin_icon_wrap_style">Стиль обёртки иконки (CSS)</label>
                        <input class="form-control form-control-sm" type="text" id="admin_icon_wrap_style" name="admin_icon_wrap_style" maxlength="255" value="<?= htmlspecialchars($form['admin_icon_wrap_style'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary mb-1" for="admin_border_style">Стиль рамки (CSS)</label>
                        <input class="form-control form-control-sm" type="text" id="admin_border_style" name="admin_border_style" maxlength="255" value="<?= htmlspecialchars($form['admin_border_style'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    </div>
                </div>
            </details>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-between">
            <a class="btn btn-outline-secondary" href="categories.php">Отмена</a>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>

    <?php if ($existing !== null) : ?>
        <form method="post" onsubmit="return confirm('Удалить эту категорию? Документов в разделе быть не должно.');">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <input type="hidden" name="_action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $existing->id ?>">
            <button type="submit" class="btn btn-outline-danger">Удалить категорию</button>
        </form>
    <?php endif; ?>
</main>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/views/admin/layout.php';
