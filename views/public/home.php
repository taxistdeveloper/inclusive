<?php

declare(strict_types=1);

/** @var list<\App\Model\Category> $categories */
/** @var array<string, list<\App\Model\Document>> $documentsBySlug */

/** Порядок и классы позиций как в статичном index.html (Downloads) */
$diagramCards = [
    ['pos' => 'card-1', 'slug' => 'family', 'extraClass' => 'top-0'],
    ['pos' => 'card-3', 'slug' => 'society', 'extraClass' => 'top-0'],
    ['pos' => 'card-2', 'slug' => 'rules', 'extraClass' => 'translate-middle-y'],
    ['pos' => 'card-4', 'slug' => 'career', 'extraClass' => 'top-50 translate-middle-y'],
    ['pos' => 'card-5', 'slug' => 'college', 'extraClass' => ''],
    ['pos' => 'card-6', 'slug' => 'tech', 'extraClass' => ''],
];
$bySlug = [];
foreach ($categories as $c) {
    $bySlug[$c->slug] = $c;
}

?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Экосистема</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <style>
      :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4895ef;
        --danger-color: #f72585;
        --warning-color: #ffd60a;
        --purple-color: #7209b7;
      }

      body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
      }

      .text-purple {
        color: var(--purple-color);
      }

      .center-icon {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        box-shadow: 0 8px 32px rgba(67, 97, 238, 0.15);
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .center-icon:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(67, 97, 238, 0.25);
      }

      .card-box {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        padding: 25px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        transition: all 0.3s ease;
        cursor: pointer;
      }

      .ecosystem-diagram-desktop .card-box {
        width: 280px;
      }

      .ecosystem-diagram--stacked .card-box {
        width: 100%;
        max-width: 100%;
      }

      .card-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 40px rgba(67, 97, 238, 0.15);
      }

      .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
      }

      .modal-header {
        border-bottom: none;
        padding: 2rem 2rem 1rem;
      }

      .modal-body {
        padding: 1rem 2rem;
      }

      .modal-footer {
        border-top: none;
        padding: 1rem 2rem 2rem;
      }

      .modal-card {
        background-color: #f8f9fa;
        border-radius: 15px;
        transition: transform 0.2s ease;
      }

      .modal-card:hover {
        transform: translateX(5px);
        background-color: #e9ecef;
      }

      .badge {
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        letter-spacing: 0.5px;
      }

      .alert {
        border: none;
        border-radius: 15px;
        background: rgba(67, 97, 238, 0.1);
        border-left: 4px solid var(--primary-color);
        padding: 1rem 1.25rem;
      }

      .btn {
        padding: 0.625rem 1.5rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.3s ease;
      }

      .component-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s ease;
      }

      .modal-card:hover .component-icon {
        background: var(--primary-color);
        transform: scale(1.1);
      }

      .modal-card:hover .component-icon i {
        color: white !important;
      }

      .components-list {
        opacity: 0;
        animation: slideIn 0.5s ease-out forwards;
      }

      @keyframes slideIn {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .modal.fade .modal-dialog {
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.3s ease-in-out;
      }

      .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
      }

      #pdfModal .modal-dialog {
        max-width: 90%;
        height: 90vh;
        margin: 2rem auto;
      }

      #pdfModal .modal-content {
        height: 100%;
      }

      #pdfModal .modal-body {
        padding: 0;
        height: calc(100% - 76px);
      }

      #pdfModal iframe {
        width: 100%;
        height: 100%;
        border: none;
      }

      .modal-card[role="button"] {
        cursor: pointer;
      }

      .modal-card[role="button"]:hover .bi-file-pdf {
        transform: scale(1.1);
      }

      .bi-file-pdf {
        transition: transform 0.3s ease;
      }
      .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border: none;
      }

      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
      }

      h4.text-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 2.25rem;
        letter-spacing: -0.5px;
      }

      .lead {
        font-size: 1.1rem;
        font-weight: 400;
        color: #6c757d;
      }

      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .card-box {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
      }

      .card-box.card-1 {
        animation-delay: 0s;
      }
      .card-box.card-2 {
        animation-delay: 0.1s;
      }
      .card-box.card-3 {
        animation-delay: 0.2s;
      }
      .card-box.card-4 {
        animation-delay: 0.3s;
      }
      .card-box.card-5 {
        animation-delay: 0.4s;
      }
      .card-box.card-6 {
        animation-delay: 0.5s;
      }

      .card-1 {
        left: 180px;
      }
      .card-2 {
        left: 120px;
        top: 51%;
      }
      .card-3 {
        right: 180px;
      }
      .card-4 {
        right: 134px;
      }
      .card-5 {
        bottom: -30px;
        left: 235px;
      }
      .card-6 {
        right: 190px;
        bottom: -20px;
      }

      .arrow {
        position: absolute;
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        height: 2px;
        transform-origin: left center;
        opacity: 0.6;
        transition: opacity 0.3s ease;
      }

      .arrow::after {
        content: "";
        position: absolute;
        right: -6px;
        top: -4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--secondary-color);
      }

      .arrow-1 {
        width: 190px;
        top: 160px;
        left: 41.5%;
        transform: translateX(-50%) rotate(27deg);
      }
      .arrow-2 {
        width: 225px;
        top: 50%;
        left: 48.5%;
        transform: translate(-10%, -50%) rotate(180deg);
      }
      .arrow-3 {
        width: 200px;
        top: 140px;
        left: 55%;
        transform: translateX(70%) rotate(150deg);
      }
      .arrow-4 {
        width: 210px;
        top: 50%;
        right: 38.5%;
        transform: translate(50%, -50%) rotate(0deg);
      }
      .arrow-5 {
        width: 200px;
        bottom: 240px;
        left: 60%;
        transform: translateX(-50%) rotate(30deg);
      }
      .arrow-6 {
        width: 150px;
        bottom: 165px;
        right: 52%;
        transform: translateX(10%) rotate(-30deg);
      }

      .arrows {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
      }

      .ecosystem-diagram-desktop {
        overflow: visible;
      }

      .ecosystem-diagram-desktop .center-icon {
        z-index: 2;
      }

      .ecosystem-diagram-desktop .card-box {
        z-index: 2;
      }
    </style>
  </head>
  <body>
    <div class="container text-center mt-5">
      <h4 class="text-primary mb-4">Экосистема инклюзивного образования</h4>
      <p class="lead text-secondary mb-5">
        Это комплексная среда, направленная на обеспечение качественного образования для всех детей,<br />
        включая детей с особыми образовательными потребностями (ООП)
      </p>

      <?php if ($categories === []) : ?>
        <div class="alert alert-warning mx-auto text-center" style="max-width: 520px">
          Разделы экосистемы не загружены. Импортируйте <code>sql/migration_categories.sql</code> или создайте категории в
          <a href="admin/categories.php">админ-панели</a>.
        </div>
      <?php else : ?>
        <div class="position-relative mt-5 ecosystem-diagram-desktop d-none d-lg-block" style="height: 600px">
          <div class="center-icon position-absolute top-50 start-50 translate-middle">
            <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2rem"></i>
          </div>
          <div class="arrows" aria-hidden="true">
            <div class="arrow arrow-1"></div>
            <div class="arrow arrow-2"></div>
            <div class="arrow arrow-3"></div>
            <div class="arrow arrow-4"></div>
            <div class="arrow arrow-5"></div>
            <div class="arrow arrow-6"></div>
          </div>
          <?php foreach ($diagramCards as $row) : ?>
            <?php
            $cat = $bySlug[$row['slug']] ?? null;
            if ($cat === null) {
                continue;
            }
            $posClass = $row['pos'];
            $extra = trim($row['extraClass']);
            ?>
            <div
              class="card-box <?= htmlspecialchars($posClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> position-absolute<?= $extra !== '' ? ' ' . htmlspecialchars($extra, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?> btn text-start"
              data-bs-toggle="modal"
              data-bs-target="#<?= htmlspecialchars($cat->modalId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            >
              <i class="bi <?= htmlspecialchars($cat->iconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> <?= htmlspecialchars($cat->diagramIconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> fs-3"></i>
              <h6 class="mt-2"><?= htmlspecialchars($cat->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h6>
              <p class="small text-muted mb-0"><?= htmlspecialchars($cat->subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="ecosystem-diagram--stacked d-lg-none">
          <div class="center-icon mb-4 mx-auto">
            <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2rem"></i>
          </div>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($categories as $cat) : ?>
              <div
                class="card-box btn w-100 text-start"
                data-bs-toggle="modal"
                data-bs-target="#<?= htmlspecialchars($cat->modalId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              >
                <i class="bi <?= htmlspecialchars($cat->iconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> <?= htmlspecialchars($cat->diagramIconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> fs-3"></i>
                <h6 class="mt-2"><?= htmlspecialchars($cat->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h6>
                <p class="small text-muted mb-0"><?= htmlspecialchars($cat->subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <footer class="text-center mt-5 py-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #eef1ff 100%); border-top: 1px solid rgba(67, 97, 238, 0.1); box-shadow: 0 -4px 20px rgba(67, 97, 238, 0.05)">
      <p class="mb-0 text-secondary">
        Разработчик
        <a href="https://shotayev.dev" class="fw-semibold text-primary text-decoration-none" target="_blank">shotayev.dev</a>
        <i class="bi bi-heart-fill text-danger mx-1"></i>
        КТСК
      </p>
      <p class="mb-0 small mt-2">
        <a href="admin/login.php" class="text-secondary text-decoration-none"><i class="bi bi-shield-check me-1"></i>Админ-панель</a>
      </p>
    </footer>
<?php
foreach ($categories as $cat) {
    $modalId = $cat->modalId;
    $badgeClass = $cat->badgeClass;
    $badgeStyle = $cat->badgeStyle;
    $iconBi = $cat->iconClass;
    $title = $cat->title;
    $subtitle = $cat->subtitle;
    $pillLabel = $cat->pillLabel;
    $documents = $documentsBySlug[$cat->slug] ?? [];
    $alertStrong = $cat->alertStrong;
    $alertText = $cat->alertText;
    require __DIR__ . '/partials/ecosystem_modal.php';
}
?>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="pdfModalLabel">Просмотр документа</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <iframe id="pdfViewer" src="" title="PDF Viewer"></iframe>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const pdfModal = document.getElementById("pdfModal");
        const pdfViewer = document.getElementById("pdfViewer");
        pdfModal.addEventListener("show.bs.modal", function (event) {
          const button = event.relatedTarget;
          const pdfPath = button.getAttribute("data-pdf");
          pdfViewer.src = pdfPath;
        });
        pdfModal.addEventListener("hidden.bs.modal", function () {
          pdfViewer.src = "";
        });
      });
    </script>
  </body>
</html>
