<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware de Headers de Sécurité
 * 
 * Ajoute les headers HTTP de sécurité recommandés.
 */
class SecurityHeadersMiddleware
{
    private array $headers;

    public function __construct(?array $customHeaders = null)
    {
        $this->headers = $customHeaders ?? $this->getDefaultHeaders();
    }

    /**
     * Traite la requête
     */
    public function handle(callable $next): mixed
    {
        // Ajouter les headers de sécurité
        foreach ($this->headers as $header => $value) {
            header("{$header}: {$value}");
        }

        return $next();
    }

    /**
     * Headers par défaut
     */
    private function getDefaultHeaders(): array
    {
        return [
            // Empêcher le clickjacking
            'X-Frame-Options' => 'SAMEORIGIN',

            // Empêcher le MIME sniffing
            'X-Content-Type-Options' => 'nosniff',

            // Protection XSS (legacy)
            'X-XSS-Protection' => '1; mode=block',

            // Referrer Policy
            'Referrer-Policy' => 'strict-origin-when-cross-origin',

            // Permissions Policy
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',

            // Content Security Policy (basique)
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data: https:",
                "connect-src 'self'",
                "frame-ancestors 'self'",
            ]),
        ];
    }

    /**
     * Ajoute HSTS (HTTPS only)
     */
    public function withHsts(int $maxAge = 31536000): self
    {
        $this->headers['Strict-Transport-Security'] = "max-age={$maxAge}; includeSubDomains";
        return $this;
    }

    /**
     * Personnalise un header
     */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Supprime un header
     */
    public function withoutHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }
}
