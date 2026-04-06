<?php

declare(strict_types=1);

namespace App;

/**
 * Валидация полей документа для админки и нормализация пути к PDF.
 */
final class DocumentInput
{
    public const SECTIONS = [
        'family' => 'Семья и родители',
        'rules' => 'Главные ориентиры и правила',
        'society' => 'Общество и сообщества',
        'career' => 'Карьерное становление',
        'college' => 'Внутриколледжная среда',
        'tech' => 'Технологии и цифровые ресурсы',
    ];

    /**
     * @param array<string, string> $sectionsMap slug => название (из таблицы categories)
     * @return array{ok: true, section: string, title: string, pdfPath: string, iconClass: string, sortOrder: int}|array{ok: false, error: string}
     */
    public static function validate(
        string $section,
        string $title,
        string $pdfPath,
        string $iconClass,
        int $sortOrder,
        array $sectionsMap
    ): array {
        if ($sectionsMap === [] && isset(self::SECTIONS[$section])) {
            $sectionsMap = self::SECTIONS;
        }
        if (!isset($sectionsMap[$section])) {
            return ['ok' => false, 'error' => 'Некорректный раздел.'];
        }
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 500) {
            return ['ok' => false, 'error' => 'Заголовок: от 1 до 500 символов.'];
        }
        $normalized = self::normalizePdfPath($pdfPath);
        if ($normalized === null) {
            return ['ok' => false, 'error' => 'Путь к PDF должен начинаться с pdf/ и не содержать ..'];
        }
        $iconClass = trim($iconClass);
        if ($iconClass === '') {
            $iconClass = 'bi-file-earmark-text-fill';
        }
        if (!preg_match('/^bi-[a-z0-9-]+$/i', $iconClass)) {
            return ['ok' => false, 'error' => 'Иконка: класс Bootstrap Icons, например bi-list-check'];
        }
        if ($sortOrder < 0 || $sortOrder > 99999) {
            return ['ok' => false, 'error' => 'Порядок: число от 0 до 99999.'];
        }

        return [
            'ok' => true,
            'section' => $section,
            'title' => $title,
            'pdfPath' => $normalized,
            'iconClass' => $iconClass,
            'sortOrder' => $sortOrder,
        ];
    }

    public static function normalizePdfPath(string $path): ?string
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }
        if (!str_starts_with($path, 'pdf/')) {
            return null;
        }
        if (!preg_match('#^pdf/.+\.pdf$#iu', $path)) {
            return null;
        }

        return $path;
    }
}
