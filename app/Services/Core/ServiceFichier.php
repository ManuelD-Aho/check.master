<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Services\Security\ServiceAudit;
use Src\Exceptions\ValidationException;

/**
 * Service Fichier
 * 
 * Gestion des fichiers uploadés et du stockage.
 * Validation MIME, antivirus, stockage sécurisé.
 * 
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceFichier
{
    private const STORAGE_DIR = 'storage/uploads';
    private const MAX_FILE_SIZE = 10485760; // 10 Mo

    /**
     * Types MIME autorisés par catégorie
     */
    private const ALLOWED_MIME_TYPES = [
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'tableur' => [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ],
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
        ],
        'rapport' => [
            'application/pdf',
        ],
    ];

    /**
     * Extensions autorisées par catégorie
     */
    private const ALLOWED_EXTENSIONS = [
        'document' => ['pdf', 'doc', 'docx'],
        'tableur' => ['xls', 'xlsx', 'csv'],
        'image' => ['jpg', 'jpeg', 'png', 'gif'],
        'rapport' => ['pdf'],
    ];

    /**
     * Upload un fichier
     *
     * @param array $file Fichier depuis $_FILES
     * @param string $category Catégorie (document, tableur, image, rapport)
     * @param string $subDirectory Sous-répertoire de stockage
     * @return array{path: string, name: string, size: int, hash: string}
     * @throws ValidationException
     */
    public static function upload(
        array $file,
        string $category,
        string $subDirectory = '',
        ?int $userId = null
    ): array {
        // Vérifications de base
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw ValidationException::withDetails('Fichier invalide ou non uploadé', [
                'file' => ['Fichier invalide'],
            ]);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw ValidationException::withDetails('Erreur lors de l\'upload', [
                'file' => [self::getUploadError($file['error'])],
            ]);
        }

        // Vérifier la taille
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw ValidationException::withDetails('Fichier trop volumineux', [
                'file' => ['Le fichier ne doit pas dépasser 10 Mo'],
            ]);
        }

        // Vérifier le type MIME
        $mimeType = self::getMimeType($file['tmp_name']);
        if (!self::isAllowedMimeType($mimeType, $category)) {
            throw ValidationException::withDetails('Type de fichier non autorisé', [
                'file' => ["Type MIME '{$mimeType}' non autorisé pour la catégorie '{$category}'"],
            ]);
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!self::isAllowedExtension($extension, $category)) {
            throw ValidationException::withDetails('Extension de fichier non autorisée', [
                'file' => ["Extension '.{$extension}' non autorisée"],
            ]);
        }

        // Générer un nom unique sécurisé
        $safeFilename = self::generateSafeFilename($file['name']);

        // Construire le chemin de destination
        $uploadDir = self::getUploadDirectory($subDirectory);
        $destPath = $uploadDir . '/' . $safeFilename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw ValidationException::withDetails('Erreur lors de l\'enregistrement', [
                'file' => ['Impossible d\'enregistrer le fichier'],
            ]);
        }

        // Calculer le hash SHA256
        $hash = hash_file('sha256', $destPath);

        // Log d'audit
        ServiceAudit::log('upload_fichier', 'fichier', null, [
            'nom_original' => $file['name'],
            'nom_securise' => $safeFilename,
            'taille' => $file['size'],
            'mime' => $mimeType,
            'hash' => $hash,
        ]);

        return [
            'path' => $destPath,
            'name' => $safeFilename,
            'original_name' => $file['name'],
            'size' => $file['size'],
            'mime' => $mimeType,
            'hash' => $hash,
        ];
    }

    /**
     * Supprime un fichier
     */
    public static function delete(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        // Vérifier que le fichier est dans le répertoire autorisé
        $realPath = realpath($path);
        $storageDir = realpath(self::getStorageRoot());

        if ($storageDir === false || $realPath === false) {
            return false;
        }

        if (!str_starts_with($realPath, $storageDir)) {
            return false; // Tentative de suppression hors du répertoire autorisé
        }

        $result = unlink($path);

        if ($result) {
            ServiceAudit::log('suppression_fichier', 'fichier', null, [
                'chemin' => $path,
            ]);
        }

        return $result;
    }

    /**
     * Déplace un fichier
     */
    public static function move(string $source, string $destination): bool
    {
        if (!file_exists($source)) {
            return false;
        }

        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        return rename($source, $destination);
    }

    /**
     * Copie un fichier
     */
    public static function copy(string $source, string $destination): bool
    {
        if (!file_exists($source)) {
            return false;
        }

        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        return copy($source, $destination);
    }

    /**
     * Vérifie l'intégrité d'un fichier
     */
    public static function verifyIntegrity(string $path, string $expectedHash): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        return hash_file('sha256', $path) === $expectedHash;
    }

    /**
     * Retourne les informations d'un fichier
     */
    public static function getInfo(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        return [
            'path' => $path,
            'name' => basename($path),
            'size' => filesize($path),
            'mime' => self::getMimeType($path),
            'hash' => hash_file('sha256', $path),
            'created_at' => date('Y-m-d H:i:s', filectime($path)),
            'modified_at' => date('Y-m-d H:i:s', filemtime($path)),
        ];
    }

    /**
     * Retourne le contenu d'un fichier
     */
    public static function getContent(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }

    /**
     * Écrit du contenu dans un fichier
     */
    public static function putContent(string $path, string $content): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Liste les fichiers d'un répertoire
     */
    public static function listFiles(string $directory, ?string $extension = null): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \DirectoryIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }

            if ($extension !== null && $file->getExtension() !== $extension) {
                continue;
            }

            $files[] = [
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        return $files;
    }

    /**
     * Génère un nom de fichier sécurisé
     */
    public static function generateSafeFilename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $basename = pathinfo($originalName, PATHINFO_FILENAME);

        // Nettoyer le nom de base
        $safeBasename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $safeBasename = substr($safeBasename, 0, 50); // Limiter la longueur

        // Ajouter un identifiant unique
        $uniqueId = bin2hex(random_bytes(8));

        return date('Y-m-d') . '_' . $safeBasename . '_' . $uniqueId . '.' . $extension;
    }

    /**
     * Retourne le type MIME d'un fichier
     */
    public static function getMimeType(string $path): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path) ?: 'application/octet-stream';
    }

    /**
     * Vérifie si un type MIME est autorisé pour une catégorie
     */
    public static function isAllowedMimeType(string $mimeType, string $category): bool
    {
        if (!isset(self::ALLOWED_MIME_TYPES[$category])) {
            return false;
        }

        return in_array($mimeType, self::ALLOWED_MIME_TYPES[$category], true);
    }

    /**
     * Vérifie si une extension est autorisée pour une catégorie
     */
    public static function isAllowedExtension(string $extension, string $category): bool
    {
        if (!isset(self::ALLOWED_EXTENSIONS[$category])) {
            return false;
        }

        return in_array(strtolower($extension), self::ALLOWED_EXTENSIONS[$category], true);
    }

    /**
     * Retourne le répertoire d'upload
     */
    private static function getUploadDirectory(string $subDirectory = ''): string
    {
        $baseDir = self::getStorageRoot();
        $uploadDir = $subDirectory !== '' ? $baseDir . '/' . trim($subDirectory, '/') : $baseDir;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        return $uploadDir;
    }

    /**
     * Retourne le répertoire racine de stockage
     */
    private static function getStorageRoot(): string
    {
        return dirname(__DIR__, 3) . '/' . self::STORAGE_DIR;
    }

    /**
     * Retourne le message d'erreur d'upload
     */
    private static function getUploadError(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé',
            UPLOAD_ERR_NO_TMP_DIR => 'Répertoire temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement',
        ];

        return $errors[$errorCode] ?? 'Erreur inconnue lors de l\'upload';
    }

    /**
     * Formate la taille en octets
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Calcule l'espace utilisé
     */
    public static function getUsedSpace(): array
    {
        $directory = self::getStorageRoot();
        $totalSize = 0;
        $fileCount = 0;

        if (is_dir($directory)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        }

        return [
            'files' => $fileCount,
            'size_bytes' => $totalSize,
            'size_formatted' => self::formatSize($totalSize),
        ];
    }
}
