<?php

declare(strict_types=1);

namespace App;

/**
 * Загрузка PDF в каталог pdf/ относительно корня сайта.
 */
final class PdfUpload
{
    public const MAX_BYTES = 25 * 1024 * 1024;

    /**
     * @param array<string, mixed> $file элемент $_FILES['…']
     * @return array{status: 'none'}|array{status: 'ok', path: string}|array{status: 'error', message: string}
     */
    public static function tryStore(array $file, string $pdfAbsoluteDir): array
    {
        if ($file === []) {
            return ['status' => 'none'];
        }
        $err = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_NO_FILE) {
            return ['status' => 'none'];
        }
        if ($err !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => 'Ошибка загрузки файла (код ' . $err . ').'];
        }
        $tmp = isset($file['tmp_name']) && is_string($file['tmp_name']) ? $file['tmp_name'] : '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['status' => 'error', 'message' => 'Файл не принят сервером.'];
        }
        $size = isset($file['size']) ? (int) $file['size'] : 0;
        if ($size <= 0 || $size > self::MAX_BYTES) {
            return ['status' => 'error', 'message' => 'Размер PDF: от 1 байт до 25 МБ.'];
        }
        if (!self::isPdfMagic($tmp)) {
            return ['status' => 'error', 'message' => 'Допустим только формат PDF.'];
        }
        if (!is_dir($pdfAbsoluteDir)) {
            if (!@mkdir($pdfAbsoluteDir, 0755, true)) {
                return ['status' => 'error', 'message' => 'Не удалось создать папку pdf/.'];
            }
        }
        if (!is_writable($pdfAbsoluteDir)) {
            return [
                'status' => 'error',
                'message' => 'Папка pdf/ недоступна для записи. На сервере выдайте права каталогу (например chmod 775) и владельца — пользователю PHP/веб-сервера (часто www-data, nginx или учётная запись сайта в панели хостинга).',
            ];
        }
        $original = isset($file['name']) && is_string($file['name']) ? $file['name'] : 'document.pdf';
        $destName = self::makeUniqueFilename($pdfAbsoluteDir, $original);
        $destAbs = $pdfAbsoluteDir . DIRECTORY_SEPARATOR . $destName;
        if (!move_uploaded_file($tmp, $destAbs)) {
            return ['status' => 'error', 'message' => 'Не удалось сохранить файл на диск.'];
        }

        return ['status' => 'ok', 'path' => 'pdf/' . str_replace('\\', '/', $destName)];
    }

    private static function isPdfMagic(string $path): bool
    {
        $h = @fopen($path, 'rb');
        if ($h === false) {
            return false;
        }
        $sig = fread($h, 5);
        fclose($h);

        return $sig === '%PDF-';
    }

    private static function makeUniqueFilename(string $dir, string $originalName): string
    {
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $base = is_string($base) ? $base : 'document';
        $base = preg_replace('/[^\p{L}\p{N}._\s\-]+/u', '_', $base) ?? 'document';
        $base = preg_replace('/\s+/u', '_', trim((string) $base));
        $base = trim((string) $base, '._-');
        if ($base === '') {
            $base = 'document';
        }
        if (strlen($base) > 100) {
            $base = substr($base, 0, 100);
        }
        $name = $base . '.pdf';
        if (!is_file($dir . DIRECTORY_SEPARATOR . $name)) {
            return $name;
        }
        $suffix = substr(bin2hex(random_bytes(4)), 0, 8);
        $name = $base . '_' . $suffix . '.pdf';
        if (!is_file($dir . DIRECTORY_SEPARATOR . $name)) {
            return $name;
        }

        return 'upload_' . bin2hex(random_bytes(8)) . '.pdf';
    }
}
