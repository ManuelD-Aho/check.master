<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour la gestion des fichiers
 * 
 * Utilitaires pour l'upload, la validation et la manipulation de fichiers.
 */
class FileHelper
{
    /**
     * Types MIME autorisés par catégorie
     */
    private const MIME_CATEGORIES = [
        'images' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'archives' => [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
        ],
    ];

    /**
     * Extensions dangereuses à bloquer
     */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
        'exe', 'bat', 'cmd', 'sh', 'bash',
        'js', 'vbs', 'ps1',
        'htaccess', 'htpasswd',
    ];

    /**
     * Taille maximale par défaut (10 Mo)
     */
    private const DEFAULT_MAX_SIZE = 10 * 1024 * 1024;

    /**
     * Génère un nom de fichier unique et sécurisé
     */
    public static function generateSafeFilename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        // Nettoyer le nom de base
        $safeName = self::sanitizeFilename($baseName);

        // Ajouter un identifiant unique
        $uniqueId = bin2hex(random_bytes(8));

        // Construire le nom final
        return sprintf('%s_%s.%s', $safeName, $uniqueId, $extension);
    }

    /**
     * Nettoie un nom de fichier
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remplacer les caractères accentués
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename) ?: $filename;

        // Ne garder que les caractères alphanumériques, tirets et underscores
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $filename) ?? '';

        // Éviter les underscores multiples
        $filename = preg_replace('/_+/', '_', $filename) ?? '';

        // Limiter la longueur
        $filename = substr($filename, 0, 100);

        // Éviter les noms vides
        if (empty($filename)) {
            $filename = 'file';
        }

        return $filename;
    }

    /**
     * Vérifie si une extension est autorisée
     *
     * @param array<string> $allowedExtensions
     */
    public static function isExtensionAllowed(string $filename, array $allowedExtensions): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Bloquer les extensions dangereuses
        if (in_array($extension, self::DANGEROUS_EXTENSIONS, true)) {
            return false;
        }

        // Vérifier si l'extension est dans la liste autorisée
        if (empty($allowedExtensions)) {
            return true;
        }

        return in_array($extension, array_map('strtolower', $allowedExtensions), true);
    }

    /**
     * Détecte le type MIME réel d'un fichier
     */
    public static function getMimeType(string $filepath): string
    {
        if (!file_exists($filepath)) {
            return 'application/octet-stream';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($filepath) ?: 'application/octet-stream';
    }

    /**
     * Vérifie si le type MIME correspond à l'extension
     */
    public static function validateMimeType(string $filepath, string $expectedExtension): bool
    {
        $mimeType = self::getMimeType($filepath);
        $extension = strtolower($expectedExtension);

        // Mapping extension -> MIME
        $mimeMap = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'zip' => ['application/zip'],
        ];

        $expectedMimes = $mimeMap[$extension] ?? [];
        return in_array($mimeType, $expectedMimes, true);
    }

    /**
     * Vérifie si un fichier est une image valide
     */
    public static function isValidImage(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $mimeType = self::getMimeType($filepath);
        if (!in_array($mimeType, self::MIME_CATEGORIES['images'], true)) {
            return false;
        }

        // Vérifier avec getimagesize
        $imageInfo = @getimagesize($filepath);
        return $imageInfo !== false;
    }

    /**
     * Retourne les dimensions d'une image
     *
     * @return array{width: int, height: int}|null
     */
    public static function getImageDimensions(string $filepath): ?array
    {
        $imageInfo = @getimagesize($filepath);
        if ($imageInfo === false) {
            return null;
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
        ];
    }

    /**
     * Calcule le hash SHA256 d'un fichier
     */
    public static function getFileHash(string $filepath): ?string
    {
        if (!file_exists($filepath)) {
            return null;
        }

        return hash_file('sha256', $filepath) ?: null;
    }

    /**
     * Déplace un fichier uploadé de manière sécurisée
     */
    public static function moveUploadedFile(string $tmpPath, string $destination): bool
    {
        // Créer le répertoire de destination si nécessaire
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }

        // Déplacer le fichier
        if (is_uploaded_file($tmpPath)) {
            return move_uploaded_file($tmpPath, $destination);
        }

        // Pour les tests ou autres cas
        return rename($tmpPath, $destination);
    }

    /**
     * Crée un répertoire de manière récursive et sécurisée
     */
    public static function createDirectory(string $path, int $permissions = 0755): bool
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, $permissions, true);
    }

    /**
     * Supprime un fichier de manière sécurisée
     */
    public static function deleteFile(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return true;
        }

        if (!is_file($filepath)) {
            return false;
        }

        return unlink($filepath);
    }

    /**
     * Supprime un répertoire et son contenu
     */
    public static function deleteDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return true;
        }

        $items = scandir($path);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($itemPath)) {
                self::deleteDirectory($itemPath);
            } else {
                unlink($itemPath);
            }
        }

        return rmdir($path);
    }

    /**
     * Formate une taille de fichier en format lisible
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $factor = 0;

        while ($bytes >= 1024 && $factor < count($units) - 1) {
            $bytes /= 1024;
            $factor++;
        }

        return round($bytes, 2) . ' ' . $units[$factor];
    }

    /**
     * Convertit une taille en bytes depuis une notation humaine
     */
    public static function parseFileSize(string $size): int
    {
        $size = strtoupper(trim($size));
        $value = (float) $size;

        if (str_ends_with($size, 'K') || str_ends_with($size, 'KO')) {
            return (int) ($value * 1024);
        }
        if (str_ends_with($size, 'M') || str_ends_with($size, 'MO')) {
            return (int) ($value * 1024 * 1024);
        }
        if (str_ends_with($size, 'G') || str_ends_with($size, 'GO')) {
            return (int) ($value * 1024 * 1024 * 1024);
        }

        return (int) $value;
    }

    /**
     * Vérifie si un chemin est dans un répertoire autorisé (évite la traversée)
     */
    public static function isPathSafe(string $path, string $basePath): bool
    {
        $realBase = realpath($basePath);
        $realPath = realpath(dirname($path));

        if ($realBase === false || $realPath === false) {
            return false;
        }

        return str_starts_with($realPath, $realBase);
    }

    /**
     * Génère un chemin de stockage basé sur la date
     */
    public static function generateDateBasedPath(string $basePath): string
    {
        $year = date('Y');
        $month = date('m');

        return sprintf('%s/%s/%s', rtrim($basePath, '/'), $year, $month);
    }

    /**
     * Retourne l'extension d'un fichier
     */
    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Vérifie si un fichier est vide
     */
    public static function isFileEmpty(string $filepath): bool
    {
        return !file_exists($filepath) || filesize($filepath) === 0;
    }

    /**
     * Copie un fichier avec vérification
     */
    public static function copyFile(string $source, string $destination): bool
    {
        if (!file_exists($source)) {
            return false;
        }

        $dir = dirname($destination);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return false;
        }

        return copy($source, $destination);
    }
}
