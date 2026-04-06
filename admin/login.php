<?php

declare(strict_types=1);

require __DIR__ . '/_init.php';

use App\Csrf;

if ($auth->check()) {
    header('Location: index.php', true, 302);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_csrf'] ?? '';
    if (!Csrf::validate(is_string($token) ? $token : null)) {
        $error = 'Сессия устарела. Обновите страницу и попробуйте снова.';
    } else {
        $login = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
        $pass = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
        if (!$auth->attempt($login, $pass)) {
            $error = 'Неверный логин или пароль.';
        } else {
            header('Location: index.php', true, 302);
            exit;
        }
    }
}

$csrf = Csrf::token();
$title = 'Вход в админку';
ob_start();
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card shadow-sm" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4">
            <h1 class="h4 mb-4 text-center">Админ-панель</h1>
            <?php if ($error !== '') : ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                <div class="mb-3">
                    <label class="form-label" for="username">Логин</label>
                    <input class="form-control" type="text" id="username" name="username" required autocomplete="username" autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Пароль</label>
                    <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
            <p class="text-center text-muted small mt-3 mb-0">
                <a href="../index.php" class="text-decoration-none">На сайт</a>
            </p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/views/admin/layout.php';
