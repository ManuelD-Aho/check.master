<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Trop de Requêtes
 * 
 * Lancée quand le rate limiting est déclenché.
 * Code HTTP: 429 Too Many Requests
 */
class TooManyRequestsException extends AppException
{
    protected int $httpCode = 429;
    protected string $errorCode = 'TOO_MANY_REQUESTS';
    private int $retryAfter;

    /**
     * @param int $retryAfter Secondes avant nouvelle tentative
     * @param string $message Message d'erreur
     */
    public function __construct(
        int $retryAfter = 60,
        string $message = 'Trop de requêtes. Veuillez patienter.'
    ) {
        parent::__construct(
            $message,
            429,
            'TOO_MANY_REQUESTS',
            ['retry_after' => $retryAfter]
        );
        $this->retryAfter = $retryAfter;
    }

    /**
     * Retourne le délai avant nouvelle tentative (secondes)
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    /**
     * {@inheritdoc}
     */
    public function respond(): void
    {
        http_response_code($this->httpCode);
        header('Content-Type: application/json; charset=utf-8');
        header("Retry-After: {$this->retryAfter}");
        echo $this->toJson();
    }

    /**
     * Rate limit connexion
     */
    public static function loginAttempts(int $retryAfter = 300): self
    {
        return new self(
            $retryAfter,
            'Trop de tentatives de connexion. Réessayez dans ' . ceil($retryAfter / 60) . ' minute(s).'
        );
    }

    /**
     * Rate limit API
     */
    public static function apiLimit(int $retryAfter = 60): self
    {
        return new self(
            $retryAfter,
            'Limite de requêtes API atteinte. Réessayez dans ' . $retryAfter . ' seconde(s).'
        );
    }

    /**
     * Rate limit email
     */
    public static function emailLimit(int $retryAfter = 3600): self
    {
        return new self(
            $retryAfter,
            "Trop d'emails envoyés. Réessayez dans " . ceil($retryAfter / 60) . ' minute(s).'
        );
    }
}
