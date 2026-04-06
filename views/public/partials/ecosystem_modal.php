<?php

declare(strict_types=1);

/** @var string $modalId */
/** @var string $badgeClass */
/** @var string $iconBi */
/** @var string $title */
/** @var string $subtitle */
/** @var string $pillLabel */
/** @var list<\App\Model\Document> $documents */
/** @var string $alertStrong */
/** @var string $alertText */
/** @var string|null $badgeStyle */

$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$badgeStyle = $badgeStyle ?? null;
?>
    <div class="modal fade" id="<?= $h($modalId) ?>" tabindex="-1" aria-labelledby="<?= $h($modalId) ?>Label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header d-flex gap-4 align-items-start">
            <div class="badge <?= $h($badgeClass) ?> p-3 rounded-circle"<?= $badgeStyle !== null ? ' style="' . $h($badgeStyle) . '"' : '' ?>>
              <i class="bi <?= $h($iconBi) ?> fs-2"></i>
            </div>
            <div class="flex-grow-1">
              <h3 class="mb-2"><?= $h($title) ?></h3>
              <p class="text-secondary mb-0"><?= $h($subtitle) ?></p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex align-items-center mb-4">
              <span class="badge bg-secondary rounded-pill"><?= $h($pillLabel) ?></span>
            </div>
            <div class="components-list">
              <?php if ($documents === []) : ?>
                <p class="text-secondary small mb-0">Документы не добавлены. Заполните список в админ-панели.</p>
              <?php else : ?>
                <?php foreach ($documents as $doc) : ?>
              <div class="card modal-card border-0 p-3 mb-3" role="button" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= $h($doc->pdfPath) ?>">
                <div class="d-flex gap-3 align-items-center justify-content-between">
                  <div class="d-flex gap-3 align-items-center">
                    <div class="component-icon">
                      <i class="bi <?= $h($doc->iconClass) ?> fs-4 text-primary"></i>
                    </div>
                    <span class="text-secondary"><?= $h($doc->title) ?></span>
                  </div>
                  <i class="bi bi-file-pdf fs-4 text-danger"></i>
                </div>
              </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="alert alert-primary mt-4">
              <i class="bi bi-info-circle-fill me-2"></i>
              <strong><?= $h($alertStrong) ?></strong> <?= $h($alertText) ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Закрыть</button>
          </div>
        </div>
      </div>
    </div>
