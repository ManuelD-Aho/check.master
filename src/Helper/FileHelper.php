<?php

declare(strict_types=1);

namespace App\Helper;

final class FileHelper
{
    private function __construct()
    {
    }

    public static function ensureDirectory(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, 0755, true);
    }

    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function getMimeType(string $path): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if ($finfo !== false) {
                $mime = finfo_file($finfo, $path);
                finfo_close($finfo);

                return $mime !== false ? $mime : 'application/octet-stream';
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($path);

            return $mime !== false ? $mime : 'application/octet-stream';
        }

        return 'application/octet-stream';
    }

    public static function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' Ko';
        }

        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' Mo';
        }

        return round($bytes / 1073741824, 2) . ' Go';
    }

    public static function isImage(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $mime = self::getMimeType($path);

        return str_starts_with($mime, 'image/');
    }

    public static function generateUniqueFilename(string $extension): string
    {
        $unique = uniqid('', true) . bin2hex(random_bytes(8));
        $extension = ltrim($extension, '.');

        return $unique . '.' . $extension;
    }

    public static function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);

        return trim($filename, '_');
    }

    public static function deleteIfExists(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
    }
}
