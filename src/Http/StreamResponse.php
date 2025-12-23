<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe StreamResponse
 * 
 * Réponse pour le streaming de fichiers volumineux.
 * Permet d'envoyer des fichiers sans charger tout le contenu en mémoire.
 */
class StreamResponse extends Response
{
    /**
     * Chemin du fichier à streamer
     */
    private string $filePath = '';

    /**
     * Callback pour générer le contenu
     *
     * @var callable|null
     */
    private $callback = null;

    /**
     * Taille du buffer (8KB par défaut)
     */
    private int $bufferSize = 8192;

    /**
     * Position de départ pour le range request
     */
    private int $startByte = 0;

    /**
     * Position de fin pour le range request
     */
    private int $endByte = -1;

    /**
     * Constructeur pour stream depuis un fichier
     *
     * @param string $filePath Chemin du fichier
     * @param int $statusCode Code HTTP
     * @param array<string, string> $headers En-têtes additionnels
     */
    public function __construct(string $filePath = '', int $statusCode = 200, array $headers = [])
    {
        parent::__construct('', $statusCode, $headers);
        
        if ($filePath !== '' && file_exists($filePath)) {
            $this->filePath = $filePath;
            $this->setupFileHeaders($filePath);
        }
    }

    /**
     * Crée un stream depuis un fichier
     *
     * @param string $filePath Chemin du fichier
     * @param string $contentType Type MIME du fichier
     */
    public static function fromFile(string $filePath, string $contentType = ''): self
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Fichier non trouvé: {$filePath}");
        }

        $response = new self($filePath);
        
        if ($contentType !== '') {
            $response->header('Content-Type', $contentType);
        }

        return $response;
    }

    /**
     * Crée un stream depuis un callback
     *
     * @param callable $callback Callback qui génère le contenu
     * @param string $contentType Type MIME
     */
    public static function fromCallback(callable $callback, string $contentType = 'application/octet-stream'): self
    {
        $response = new self();
        $response->callback = $callback;
        $response->header('Content-Type', $contentType);
        $response->header('Transfer-Encoding', 'chunked');

        return $response;
    }

    /**
     * Configure les en-têtes pour un fichier
     */
    private function setupFileHeaders(string $filePath): void
    {
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        $this->header('Content-Type', $mimeType);
        $this->header('Content-Length', (string) $fileSize);
        $this->header('Accept-Ranges', 'bytes');
    }

    /**
     * Définit la taille du buffer
     */
    public function setBufferSize(int $size): self
    {
        $this->bufferSize = max(1024, $size);
        return $this;
    }

    /**
     * Active le support des range requests (téléchargement partiel)
     */
    public function withRangeSupport(): self
    {
        // Vérifier si un range est demandé
        $rangeHeader = $_SERVER['HTTP_RANGE'] ?? '';
        
        if ($rangeHeader !== '' && preg_match('/bytes=(\d+)-(\d*)/', $rangeHeader, $matches)) {
            $this->startByte = (int) $matches[1];
            $this->endByte = $matches[2] !== '' ? (int) $matches[2] : -1;

            if ($this->filePath !== '' && file_exists($this->filePath)) {
                $fileSize = filesize($this->filePath);
                $this->endByte = $this->endByte < 0 ? $fileSize - 1 : min($this->endByte, $fileSize - 1);
                $contentLength = $this->endByte - $this->startByte + 1;

                $this->setStatusCode(206);
                $this->header('Content-Range', "bytes {$this->startByte}-{$this->endByte}/{$fileSize}");
                $this->header('Content-Length', (string) $contentLength);
            }
        }

        return $this;
    }

    /**
     * Envoie la réponse en streaming
     */
    public function send(): void
    {
        // Envoyer le code de statut
        http_response_code($this->statusCode);

        // Envoyer les headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Désactiver le buffering de sortie avec limite pour éviter boucle infinie
        $maxIterations = 100;
        $iterations = 0;
        while (ob_get_level() > 0 && $iterations < $maxIterations) {
            ob_end_flush();
            $iterations++;
        }

        // Stream depuis un fichier
        if ($this->filePath !== '' && file_exists($this->filePath)) {
            $this->streamFile();
            return;
        }

        // Stream depuis un callback
        if ($this->callback !== null) {
            $this->streamCallback();
            return;
        }

        // Fallback: envoyer le contenu normal
        echo $this->content;
    }

    /**
     * Stream un fichier
     */
    private function streamFile(): void
    {
        $handle = fopen($this->filePath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier: {$this->filePath}");
        }

        try {
            // Se positionner au bon endroit pour les range requests
            if ($this->startByte > 0) {
                fseek($handle, $this->startByte);
            }

            $remaining = $this->endByte >= 0 
                ? $this->endByte - $this->startByte + 1 
                : PHP_INT_MAX;

            // Lire et envoyer par chunks
            while (!feof($handle) && $remaining > 0) {
                $readSize = min($this->bufferSize, $remaining);
                $chunk = fread($handle, $readSize);
                
                if ($chunk === false) {
                    break;
                }

                echo $chunk;
                flush();
                
                $remaining -= strlen($chunk);

                // Vérifier si la connexion est fermée
                if (connection_aborted()) {
                    break;
                }
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Stream depuis un callback
     */
    private function streamCallback(): void
    {
        if ($this->callback === null) {
            return;
        }

        $callback = $this->callback;
        
        // Le callback doit écrire directement avec echo/print
        $callback();
    }

    /**
     * Retourne le chemin du fichier
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Vérifie si c'est un range request
     */
    public function isRangeRequest(): bool
    {
        return $this->startByte > 0 || $this->endByte >= 0;
    }
}
