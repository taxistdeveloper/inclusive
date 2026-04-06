<?php

declare(strict_types=1);

namespace App\Model;

final class Document
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $section,
        public readonly string $title,
        public readonly string $pdfPath,
        public readonly string $iconClass,
        public readonly int $sortOrder,
    ) {
    }
}
