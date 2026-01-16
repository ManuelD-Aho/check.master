<?php

declare(strict_types=1);

namespace Src\Upload;

use Src\Exceptions\ValidationException;
use Src\Support\Str;

/**
 * Upload Handler - Gestion avancée des uploads de fichiers
 * 
 * Fonctionnalités:
 * - Validation de type MIME
 * - Validation de taille
 * - Détection de virus (ClamAV integration)
 * - Génération de noms uniques sécurisés
 * - Support multi-fichiers
 * - Validation d'images (dimensions, ratio)
 * - Chunked upload pour gros fichiers
 * - Stockage organisé par date
 * 
 * @package Src\Upload
 */
class UploadHandler
{
    private array $config;
    private array $allowedMimes = [];
    private int $maxFileSize;
    private string $uploadPath;
    private bool $virusScanEnabled;
    private bool $organizeByDate;

    /**
     * Constructeur
     *
     * @param array $config Configuration upload
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->allowedMimes = $config['allowed_mimes'] ?? ['image/jpeg', 'image/png', 'application/pdf'];
        $this->maxFileSize = $config['max_file_size'] ?? 10 * 1024 * 1024; // 10MB
        $this->uploadPath = $config['upload_path'] ?? __DIR__ . '/../../storage/uploads';
        $this->virusScanEnabled = $config['virus_scan'] ?? false;
        $this->organizeByDate = $config['organize_by_date'] ?? true;

        // Créer le dossier si inexistant
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Uploader un fichier unique
     *
     * @param array $file Fichier $_FILES['field']
     * @param array $options Options supplémentaires
     * @return array Informations du fichier uploadé
     * @throws ValidationException
     */
    public function upload(array $file, array $options = []): array
    {
        // Validation basique
        $this->validateUpload($file);

        // Valider le type MIME
        $this->validateMimeType($file);

        // Valider la taille
        $this->validateSize($file);

        // Scan antivirus si activé
        if ($this->virusScanEnabled) {
            $this->scanVirus($file['tmp_name']);
        }

        // Validation spécifique images
        if ($this->isImage($file)) {
            $this->validateImage($file, $options);
        }

        // Générer un nom unique
        $filename = $this->generateUniqueFilename($file['name']);

        // Déterminer le chemin de destination
        $destinationPath = $this->getDestinationPath($options);
        $fullPath = $destinationPath . '/' . $filename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new ValidationException(['upload' => ["Échec de l'upload du fichier"]]);
        }

        // Chmod pour sécurité
        chmod($fullPath, 0644);

        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'path' => $fullPath,
            'relative_path' => str_replace($this->uploadPath, '', $fullPath),
            'size' => $file['size'],
            'mime_type' => $file['type'],
            'extension' => $this->getExtension($file['name']),
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Uploader plusieurs fichiers
     *
     * @param array $files Tableau de fichiers
     * @param array $options Options
     * @return array Tableau des fichiers uploadés
     * @throws ValidationException
     */
    public function uploadMultiple(array $files, array $options = []): array
    {
        $uploaded = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                $uploaded[] = $this->upload($file, $options);
            } catch (ValidationException $e) {
                $errors[$index] = $e->getErrors();
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $uploaded;
    }

    /**
     * Upload par chunks (gros fichiers)
     *
     * @param string $chunkPath Chemin du chunk
     * @param int $chunkIndex Index du chunk
     * @param int $totalChunks Total de chunks
     * @param string $fileIdentifier Identifiant unique du fichier
     * @return array|null Info du fichier si dernier chunk, null sinon
     * @throws ValidationException
     */
    public function uploadChunk(
        string $chunkPath,
        int $chunkIndex,
        int $totalChunks,
        string $fileIdentifier
    ): ?array {
        $tempDir = $this->uploadPath . '/chunks/' . $fileIdentifier;

        // Créer dossier temporaire
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Sauvegarder le chunk
        $chunkFile = $tempDir . '/chunk_' . $chunkIndex;
        if (!move_uploaded_file($chunkPath, $chunkFile)) {
            throw new ValidationException(['chunk' => ["Échec sauvegarde du chunk {$chunkIndex}"]]);
        }

        // Si tous les chunks sont reçus, assembler
        if ($this->allChunksReceived($tempDir, $totalChunks)) {
            return $this->assembleChunks($tempDir, $totalChunks, $fileIdentifier);
        }

        return null;
    }

    /**
     * Vérifier si tous les chunks sont reçus
     *
     * @param string $tempDir Dossier temporaire
     * @param int $totalChunks Total attendu
     * @return bool
     */
    private function allChunksReceived(string $tempDir, int $totalChunks): bool
    {
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!file_exists($tempDir . '/chunk_' . $i)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assembler les chunks en fichier final
     *
     * @param string $tempDir Dossier temporaire
     * @param int $totalChunks Nombre de chunks
     * @param string $fileIdentifier Identifiant fichier
     * @return array Info du fichier assemblé
     * @throws ValidationException
     */
    private function assembleChunks(string $tempDir, int $totalChunks, string $fileIdentifier): array
    {
        $finalFilename = $this->generateUniqueFilename($fileIdentifier);
        $destinationPath = $this->getDestinationPath([]);
        $finalPath = $destinationPath . '/' . $finalFilename;

        // Ouvrir le fichier final
        $finalFile = fopen($finalPath, 'wb');
        if ($finalFile === false) {
            throw new ValidationException(['assembly' => ["Impossible de créer le fichier final"]]);
        }

        // Assembler les chunks
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $tempDir . '/chunk_' . $i;
            $chunkData = file_get_contents($chunkPath);
            fwrite($finalFile, $chunkData);
            unlink($chunkPath); // Supprimer le chunk
        }

        fclose($finalFile);

        // Nettoyer le dossier temporaire
        rmdir($tempDir);

        // Obtenir les infos du fichier
        $fileSize = filesize($finalPath);
        $mimeType = mime_content_type($finalPath);

        return [
            'filename' => $finalFilename,
            'original_name' => $fileIdentifier,
            'path' => $finalPath,
            'relative_path' => str_replace($this->uploadPath, '', $finalPath),
            'size' => $fileSize,
            'mime_type' => $mimeType,
            'extension' => $this->getExtension($fileIdentifier),
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Valider l'upload de base
     *
     * @param array $file Fichier
     * @return void
     * @throws ValidationException
     */
    private function validateUpload(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new ValidationException(['upload' => ['Paramètres upload invalides']]);
        }

        // Codes d'erreur PHP
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ValidationException(['size' => ['Fichier trop volumineux']]);
            case UPLOAD_ERR_PARTIAL:
                throw new ValidationException(['upload' => ['Upload partiel']]);
            case UPLOAD_ERR_NO_FILE:
                throw new ValidationException(['upload' => ['Aucun fichier uploadé']]);
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new ValidationException(['upload' => ['Dossier temporaire manquant']]);
            case UPLOAD_ERR_CANT_WRITE:
                throw new ValidationException(['upload' => ['Échec écriture disque']]);
            case UPLOAD_ERR_EXTENSION:
                throw new ValidationException(['upload' => ['Extension PHP a arrêté l\'upload']]);
            default:
                throw new ValidationException(['upload' => ['Erreur upload inconnue']]);
        }

        // Vérifier que c'est un upload valide
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new ValidationException(['upload' => ['Fichier non uploadé via POST']]);
        }
    }

    /**
     * Valider le type MIME
     *
     * @param array $file Fichier
     * @return void
     * @throws ValidationException
     */
    private function validateMimeType(array $file): void
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedMimes)) {
            throw new ValidationException([
                'mime' => [
                    "Type de fichier non autorisé: {$mimeType}. " .
                    "Types autorisés: " . implode(', ', $this->allowedMimes)
                ]
            ]);
        }
    }

    /**
     * Valider la taille du fichier
     *
     * @param array $file Fichier
     * @return void
     * @throws ValidationException
     */
    private function validateSize(array $file): void
    {
        if ($file['size'] > $this->maxFileSize) {
            $maxMB = round($this->maxFileSize / 1024 / 1024, 2);
            $fileMB = round($file['size'] / 1024 / 1024, 2);

            throw new ValidationException([
                'size' => [
                    "Fichier trop volumineux: {$fileMB}MB. Maximum: {$maxMB}MB"
                ]
            ]);
        }

        // Vérifier que le fichier n'est pas vide
        if ($file['size'] === 0) {
            throw new ValidationException(['size' => ['Fichier vide']]);
        }
    }

    /**
     * Scanner le fichier avec ClamAV
     *
     * @param string $filepath Chemin du fichier
     * @return void
     * @throws ValidationException
     */
    private function scanVirus(string $filepath): void
    {
        // Vérifier si ClamAV est disponible
        $clamavPath = '/usr/bin/clamscan';

        if (!file_exists($clamavPath)) {
            // Log warning mais ne pas bloquer
            error_log("ClamAV non disponible pour scan antivirus");
            return;
        }

        $output = [];
        $returnVar = 0;

        exec("$clamavPath --no-summary '$filepath'", $output, $returnVar);

        // returnVar = 0 : OK, 1 : virus trouvé, 2 : erreur
        if ($returnVar === 1) {
            throw new ValidationException([
                'virus' => ['Virus détecté dans le fichier. Upload refusé.']
            ]);
        }
    }

    /**
     * Vérifier si le fichier est une image
     *
     * @param array $file Fichier
     * @return bool
     */
    private function isImage(array $file): bool
    {
        return strpos($file['type'], 'image/') === 0;
    }

    /**
     * Valider une image (dimensions, ratio)
     *
     * @param array $file Fichier
     * @param array $options Options de validation
     * @return void
     * @throws ValidationException
     */
    private function validateImage(array $file, array $options): void
    {
        $imageInfo = getimagesize($file['tmp_name']);

        if ($imageInfo === false) {
            throw new ValidationException(['image' => ['Fichier image invalide']]);
        }

        [$width, $height] = $imageInfo;

        // Valider dimensions minimales
        if (isset($options['min_width']) && $width < $options['min_width']) {
            throw new ValidationException([
                'dimensions' => ["Largeur minimum: {$options['min_width']}px. Reçu: {$width}px"]
            ]);
        }

        if (isset($options['min_height']) && $height < $options['min_height']) {
            throw new ValidationException([
                'dimensions' => ["Hauteur minimum: {$options['min_height']}px. Reçu: {$height}px"]
            ]);
        }

        // Valider dimensions maximales
        if (isset($options['max_width']) && $width > $options['max_width']) {
            throw new ValidationException([
                'dimensions' => ["Largeur maximum: {$options['max_width']}px. Reçu: {$width}px"]
            ]);
        }

        if (isset($options['max_height']) && $height > $options['max_height']) {
            throw new ValidationException([
                'dimensions' => ["Hauteur maximum: {$options['max_height']}px. Reçu: {$height}px"]
            ]);
        }

        // Valider ratio
        if (isset($options['aspect_ratio'])) {
            $ratio = $width / $height;
            $expectedRatio = $options['aspect_ratio'];
            $tolerance = $options['aspect_ratio_tolerance'] ?? 0.1;

            if (abs($ratio - $expectedRatio) > $tolerance) {
                throw new ValidationException([
                    'ratio' => ["Ratio d'aspect invalide. Attendu: {$expectedRatio}, Reçu: {$ratio}"]
                ]);
            }
        }
    }

    /**
     * Générer un nom de fichier unique et sécurisé
     *
     * @param string $originalName Nom original
     * @return string Nom sécurisé unique
     */
    private function generateUniqueFilename(string $originalName): string
    {
        $extension = $this->getExtension($originalName);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);

        // Nettoyer le nom de base
        $basename = Str::slug($basename);

        // Ajouter timestamp et hash unique
        $uniquePart = time() . '_' . bin2hex(random_bytes(8));

        return $basename . '_' . $uniquePart . '.' . $extension;
    }

    /**
     * Obtenir l'extension du fichier
     *
     * @param string $filename Nom du fichier
     * @return string Extension en minuscule
     */
    private function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Obtenir le chemin de destination
     *
     * @param array $options Options (peut contenir 'subfolder')
     * @return string Chemin complet
     */
    private function getDestinationPath(array $options): string
    {
        $path = $this->uploadPath;

        // Organisation par date
        if ($this->organizeByDate) {
            $path .= '/' . date('Y/m');
        }

        // Sous-dossier personnalisé
        if (isset($options['subfolder'])) {
            $path .= '/' . trim($options['subfolder'], '/');
        }

        // Créer le dossier si nécessaire
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }

    /**
     * Supprimer un fichier uploadé
     *
     * @param string $filepath Chemin du fichier
     * @return bool Succès
     */
    public function delete(string $filepath): bool
    {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }

        return false;
    }

    /**
     * Obtenir les informations d'un fichier
     *
     * @param string $filepath Chemin du fichier
     * @return array|null Informations ou null si non trouvé
     */
    public function getFileInfo(string $filepath): ?array
    {
        if (!file_exists($filepath)) {
            return null;
        }

        return [
            'filename' => basename($filepath),
            'path' => $filepath,
            'size' => filesize($filepath),
            'mime_type' => mime_content_type($filepath),
            'modified_at' => date('Y-m-d H:i:s', filemtime($filepath))
        ];
    }

    /**
     * Définir les types MIME autorisés
     *
     * @param array $mimes Types MIME
     * @return self
     */
    public function setAllowedMimes(array $mimes): self
    {
        $this->allowedMimes = $mimes;
        return $this;
    }

    /**
     * Définir la taille maximum
     *
     * @param int $bytes Taille en bytes
     * @return self
     */
    public function setMaxFileSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;
        return $this;
    }
}
