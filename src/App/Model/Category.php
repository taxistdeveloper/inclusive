<?php

declare(strict_types=1);

namespace App\Model;

final class Category
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $slug,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly string $iconClass,
        public readonly string $pillLabel,
        public readonly string $badgeClass,
        public readonly ?string $badgeStyle,
        public readonly string $alertStrong,
        public readonly string $alertText,
        public readonly string $modalId,
        public readonly int $sortOrder,
        public readonly string $adminIconWrapClass,
        public readonly string $adminBorderClass,
        public readonly ?string $adminIconWrapStyle,
        public readonly ?string $adminBorderStyle,
        public readonly string $diagramIconClass,
    ) {
    }
}
