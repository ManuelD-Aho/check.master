<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe Response pour les réponses HTTP
 */
class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Définit le code de statut HTTP
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Retourne le code de statut
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Ajoute un header
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Définit le contenu
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Retourne le contenu
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Envoie la réponse
     */
    public function send(): void
    {
        // Définir le code de statut
        http_response_code($this->statusCode);

        // Envoyer les headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Envoyer le contenu
        echo $this->content;
    }

    /**
     * Créer une réponse de redirection
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        if ($url !== '' && str_starts_with($url, '/')) {
            $basePath = Request::basePath();
            if ($basePath !== '' && !str_starts_with($url, $basePath . '/') && $url !== $basePath) {
                $url = $basePath . $url;
            }
        }

        return (new self('', $statusCode))
            ->header('Location', $url);
    }

    /**
     * Créer une réponse HTML
     */
    public static function html(string $content, int $statusCode = 200): self
    {
        return (new self($content, $statusCode))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Créer une réponse texte
     */
    public static function text(string $content, int $statusCode = 200): self
    {
        return (new self($content, $statusCode))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
