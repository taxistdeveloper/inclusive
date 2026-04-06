<?php

declare(strict_types=1);

/** @var list<\App\Model\Category> $categories */
/** @var array<string, list<\App\Model\Document>> $documentsBySlug */

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
        max-width: 100%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        transition: all 0.3s ease;
        cursor: pointer;
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
        <div class="center-icon mb-4 mx-auto">
          <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2rem"></i>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center mb-2">
          <?php foreach ($categories as $cat) : ?>
            <div class="col d-flex justify-content-center">
              <div
                class="card-box btn"
                data-bs-toggle="modal"
                data-bs-target="#<?= htmlspecialchars($cat->modalId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              >
                <i class="bi <?= htmlspecialchars($cat->iconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> <?= htmlspecialchars($cat->diagramIconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> fs-3"></i>
                <h6 class="mt-2"><?= htmlspecialchars($cat->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h6>
                <p class="small text-muted mb-0"><?= htmlspecialchars($cat->subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <footer class="text-center mt-5 py-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #eef1ff 100%); border-top: 1px solid rgba(67, 97, 238, 0.1); box-shadow: 0 -4px 20px rgba(67, 97, 238, 0.05)">
      <p class="mb-2 text-secondary">
        Разработчик
        <a href="https://shotayev.dev" class="fw-semibold text-primary text-decoration-none" target="_blank">shotayev.dev</a>
        <i class="bi bi-heart-fill text-danger mx-1"></i>
        КТСК
      </p>
      <p class="mb-0 small">
        <a href="admin/login.php" class="text-secondary text-decoration-none"><i class="bi bi-shield-lock me-1"></i>Админ-панель</a>
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
