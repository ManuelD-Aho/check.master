<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe DownloadResponse
 * 
 * Réponse pour le téléchargement de fichiers.
 * Configure automatiquement les en-têtes pour forcer le téléchargement.
 */
class DownloadResponse extends StreamResponse
{
    /**
     * Nom du fichier pour le téléchargement
     */
    private string $filename = '';

    /**
     * Constructeur
     *
     * @param string $filePath Chemin du fichier à télécharger
     * @param string $filename Nom du fichier pour le téléchargement (optionnel)
     * @param bool $inline Si true, affiche inline plutôt que téléchargement
     */
    public function __construct(string $filePath, string $filename = '', bool $inline = false)
    {
        parent::__construct($filePath);
        
        $this->filename = $filename !== '' ? $filename : basename($filePath);
        $this->setupDownloadHeaders($inline);
    }

    /**
     * Crée une réponse de téléchargement depuis un fichier
     *
     * @param string $filePath Chemin du fichier
     * @param string $filename Nom pour le téléchargement
     */
    public static function file(string $filePath, string $filename = ''): self
    {
        return new self($filePath, $filename, false);
    }

    /**
     * Crée une réponse inline (affichage dans le navigateur)
     *
     * @param string $filePath Chemin du fichier
     * @param string $filename Nom du fichier
     */
    public static function inline(string $filePath, string $filename = ''): self
    {
        return new self($filePath, $filename, true);
    }

    /**
     * Crée une réponse de téléchargement depuis un contenu en mémoire
     *
     * @param string $content Contenu du fichier
     * @param string $filename Nom du fichier
     * @param string $contentType Type MIME
     */
    public static function fromContent(string $content, string $filename, string $contentType = 'application/octet-stream'): Response
    {
        $response = new Response($content, 200);
        
        // Nettoyer le nom de fichier
        $safeName = self::sanitizeFilename($filename);
        
        $response->header('Content-Type', $contentType);
        $response->header('Content-Length', (string) strlen($content));
        $response->header('Content-Disposition', "attachment; filename=\"{$safeName}\"");
        $response->header('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }

    /**
     * Crée une réponse PDF
     *
     * @param string $filePath Chemin du fichier PDF
     * @param string $filename Nom pour le téléchargement
     * @param bool $inline Si true, affiche dans le navigateur
     */
    public static function pdf(string $filePath, string $filename = '', bool $inline = true): self
    {
        $response = new self($filePath, $filename ?: basename($filePath), $inline);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }

    /**
     * Crée une réponse PDF depuis un contenu en mémoire
     *
     * @param string $pdfContent Contenu PDF
     * @param string $filename Nom du fichier
     * @param bool $inline Afficher inline
     */
    public static function pdfFromContent(string $pdfContent, string $filename, bool $inline = true): Response
    {
        $response = new Response($pdfContent, 200);
        $safeName = self::sanitizeFilename($filename);
        $disposition = $inline ? 'inline' : 'attachment';
        
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Length', (string) strlen($pdfContent));
        $response->header('Content-Disposition', "{$disposition}; filename=\"{$safeName}\"");
        $response->header('Cache-Control', 'public, max-age=0');

        return $response;
    }

    /**
     * Crée une réponse Excel
     *
     * @param string $filePath Chemin du fichier Excel
     * @param string $filename Nom pour le téléchargement
     */
    public static function excel(string $filePath, string $filename = ''): self
    {
        $response = new self($filePath, $filename ?: basename($filePath), false);
        
        // Détecter le format (xlsx ou xls)
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            default => 'application/octet-stream',
        };
        
        $response->header('Content-Type', $contentType);
        return $response;
    }

    /**
     * Crée une réponse CSV
     *
     * @param string $content Contenu CSV
     * @param string $filename Nom du fichier
     */
    public static function csv(string $content, string $filename): Response
    {
        return self::fromContent(
            $content,
            $filename,
            'text/csv; charset=UTF-8'
        )->header('Content-Disposition', 'attachment; filename="' . self::sanitizeFilename($filename) . '"');
    }

    /**
     * Crée une réponse image
     *
     * @param string $filePath Chemin de l'image
     * @param bool $inline Afficher inline
     */
    public static function image(string $filePath, bool $inline = true): self
    {
        $response = new self($filePath, basename($filePath), $inline);
        
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        $response->header('Content-Type', $mimeType);
        
        // Cache pour les images
        if ($inline) {
            $response->header('Cache-Control', 'public, max-age=86400');
        }

        return $response;
    }

    /**
     * Configure les en-têtes de téléchargement
     */
    private function setupDownloadHeaders(bool $inline): void
    {
        $disposition = $inline ? 'inline' : 'attachment';
        $safeName = self::sanitizeFilename($this->filename);

        $this->header('Content-Disposition', "{$disposition}; filename=\"{$safeName}\"");
        $this->header('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $this->header('Pragma', 'no-cache');
        $this->header('Expires', '0');
        $this->header('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Nettoie un nom de fichier pour éviter les injections
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^\p{L}\p{N}\s\-_\.]/u', '', $filename) ?? $filename;
        
        // Remplacer les espaces multiples
        $filename = preg_replace('/\s+/', ' ', $filename) ?? $filename;
        
        // Limiter la longueur
        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = substr($name, 0, 195 - strlen($extension));
            $filename = $name . '.' . $extension;
        }

        return trim($filename) ?: 'download';
    }

    /**
     * Force le téléchargement plutôt que l'affichage
     */
    public function forceDownload(): self
    {
        $safeName = self::sanitizeFilename($this->filename);
        $this->header('Content-Disposition', "attachment; filename=\"{$safeName}\"");
        return $this;
    }

    /**
     * Active le support des téléchargements partiels (resume)
     */
    public function withResumeSupport(): self
    {
        return $this->withRangeSupport();
    }

    /**
     * Retourne le nom du fichier
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}
