<?php

declare(strict_types=1);

namespace App;

use App\Model\Category;

/**
 * Валидация полей категории (раздела экосистемы) для админки.
 */
final class CategoryInput
{
    /**
     * @return array{ok: true, category: Category}|array{ok: false, error: string}
     */
    public static function validateNew(
        string $slug,
        string $title,
        string $subtitle,
        string $iconClass,
        string $pillLabel,
        string $badgeClass,
        string $badgeStyle,
        string $alertStrong,
        string $alertText,
        string $modalId,
        int $sortOrder,
        string $adminIconWrapClass,
        string $adminBorderClass,
        string $adminIconWrapStyle,
        string $adminBorderStyle,
        string $diagramIconClass,
    ): array {
        $slug = trim($slug);
        if (!preg_match('/^[a-z][a-z0-9_]{1,29}$/', $slug)) {
            return ['ok' => false, 'error' => 'Ключ (slug): латиница, цифры и _, от 2 до 30 символов, с буквы.'];
        }
        $v = self::validateCommon(
            $title,
            $subtitle,
            $iconClass,
            $pillLabel,
            $badgeClass,
            $badgeStyle,
            $alertStrong,
            $alertText,
            $modalId,
            $sortOrder,
            $adminIconWrapClass,
            $adminBorderClass,
            $adminIconWrapStyle,
            $adminBorderStyle,
            $diagramIconClass
        );
        if (!$v['ok']) {
            return $v;
        }
        $c = $v['category'];

        return [
            'ok' => true,
            'category' => new Category(
                null,
                $slug,
                $c->title,
                $c->subtitle,
                $c->iconClass,
                $c->pillLabel,
                $c->badgeClass,
                $c->badgeStyle,
                $c->alertStrong,
                $c->alertText,
                $c->modalId,
                $c->sortOrder,
                $c->adminIconWrapClass,
                $c->adminBorderClass,
                $c->adminIconWrapStyle,
                $c->adminBorderStyle,
                $c->diagramIconClass,
            ),
        ];
    }

    /**
     * @return array{ok: true, category: Category}|array{ok: false, error: string}
     */
    public static function validateExisting(
        int $id,
        string $slugKeep,
        string $title,
        string $subtitle,
        string $iconClass,
        string $pillLabel,
        string $badgeClass,
        string $badgeStyle,
        string $alertStrong,
        string $alertText,
        string $modalId,
        int $sortOrder,
        string $adminIconWrapClass,
        string $adminBorderClass,
        string $adminIconWrapStyle,
        string $adminBorderStyle,
        string $diagramIconClass,
    ): array {
        $v = self::validateCommon(
            $title,
            $subtitle,
            $iconClass,
            $pillLabel,
            $badgeClass,
            $badgeStyle,
            $alertStrong,
            $alertText,
            $modalId,
            $sortOrder,
            $adminIconWrapClass,
            $adminBorderClass,
            $adminIconWrapStyle,
            $adminBorderStyle,
            $diagramIconClass
        );
        if (!$v['ok']) {
            return $v;
        }
        $c = $v['category'];
        $slugKeep = trim($slugKeep);

        return [
            'ok' => true,
            'category' => new Category(
                $id,
                $slugKeep,
                $c->title,
                $c->subtitle,
                $c->iconClass,
                $c->pillLabel,
                $c->badgeClass,
                $c->badgeStyle,
                $c->alertStrong,
                $c->alertText,
                $c->modalId,
                $c->sortOrder,
                $c->adminIconWrapClass,
                $c->adminBorderClass,
                $c->adminIconWrapStyle,
                $c->adminBorderStyle,
                $c->diagramIconClass,
            ),
        ];
    }

    /**
     * @return array{ok: true, category: Category}|array{ok: false, error: string}
     */
    private static function validateCommon(
        string $title,
        string $subtitle,
        string $iconClass,
        string $pillLabel,
        string $badgeClass,
        string $badgeStyle,
        string $alertStrong,
        string $alertText,
        string $modalId,
        int $sortOrder,
        string $adminIconWrapClass,
        string $adminBorderClass,
        string $adminIconWrapStyle,
        string $adminBorderStyle,
        string $diagramIconClass,
    ): array {
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 255) {
            return ['ok' => false, 'error' => 'Название: от 1 до 255 символов.'];
        }
        $subtitle = trim($subtitle);
        if (mb_strlen($subtitle) > 500) {
            return ['ok' => false, 'error' => 'Подзаголовок: не более 500 символов.'];
        }
        $iconClass = trim($iconClass);
        if ($iconClass === '') {
            $iconClass = 'bi-circle-fill';
        }
        if (!preg_match('/^bi-[a-z0-9-]+$/i', $iconClass)) {
            return ['ok' => false, 'error' => 'Иконка: класс Bootstrap Icons, например bi-globe'];
        }
        $pillLabel = trim($pillLabel);
        if ($pillLabel === '' || mb_strlen($pillLabel) > 128) {
            return ['ok' => false, 'error' => 'Подпись списка: 1–128 символов.'];
        }
        $badgeClass = trim($badgeClass);
        if ($badgeClass === '' || mb_strlen($badgeClass) > 128) {
            return ['ok' => false, 'error' => 'Класс бейджа: 1–128 символов (можно несколько через пробел).'];
        }
        $badgeStyle = trim($badgeStyle);
        if (mb_strlen($badgeStyle) > 255) {
            return ['ok' => false, 'error' => 'Стиль бейджа: не более 255 символов.'];
        }
        $badgeStyleNorm = $badgeStyle === '' ? null : $badgeStyle;
        $alertStrong = trim($alertStrong);
        if ($alertStrong === '' || mb_strlen($alertStrong) > 128) {
            return ['ok' => false, 'error' => 'Заголовок блока «важно»: 1–128 символов.'];
        }
        $alertText = trim($alertText);
        if (mb_strlen($alertText) > 1000) {
            return ['ok' => false, 'error' => 'Текст подсказки: не более 1000 символов.'];
        }
        $modalId = trim($modalId);
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{1,62}$/', $modalId)) {
            return ['ok' => false, 'error' => 'ID модального окна: латиница, цифры, _, -, 3–63 символа.'];
        }
        if ($sortOrder < 0 || $sortOrder > 99999) {
            return ['ok' => false, 'error' => 'Порядок: число от 0 до 99999.'];
        }
        $adminIconWrapClass = trim($adminIconWrapClass);
        if ($adminIconWrapClass === '' || mb_strlen($adminIconWrapClass) > 255) {
            return ['ok' => false, 'error' => 'Класс иконки в админке: 1–255 символов.'];
        }
        $adminBorderClass = trim($adminBorderClass);
        if (mb_strlen($adminBorderClass) > 255) {
            return ['ok' => false, 'error' => 'Класс рамки карточки: не более 255 символов.'];
        }
        $adminIconWrapStyle = trim($adminIconWrapStyle);
        if (mb_strlen($adminIconWrapStyle) > 255) {
            return ['ok' => false, 'error' => 'Стиль иконки в админке: не более 255 символов.'];
        }
        $adminIconWrapStyleNorm = $adminIconWrapStyle === '' ? null : $adminIconWrapStyle;
        $adminBorderStyle = trim($adminBorderStyle);
        if (mb_strlen($adminBorderStyle) > 255) {
            return ['ok' => false, 'error' => 'Стиль рамки: не более 255 символов.'];
        }
        $adminBorderStyleNorm = $adminBorderStyle === '' ? null : $adminBorderStyle;
        $diagramIconClass = trim($diagramIconClass);
        if ($diagramIconClass === '' || mb_strlen($diagramIconClass) > 64) {
            return ['ok' => false, 'error' => 'Класс цвета иконки на главной: 1–64 символов (например text-primary).'];
        }

        $c = new Category(
            null,
            '',
            $title,
            $subtitle,
            $iconClass,
            $pillLabel,
            $badgeClass,
            $badgeStyleNorm,
            $alertStrong,
            $alertText,
            $modalId,
            $sortOrder,
            $adminIconWrapClass,
            $adminBorderClass,
            $adminIconWrapStyleNorm,
            $adminBorderStyleNorm,
            $diagramIconClass,
        );

        return ['ok' => true, 'category' => $c];
    }
}
