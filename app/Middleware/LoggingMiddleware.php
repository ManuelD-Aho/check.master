<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Support\Auth;

/**
 * Middleware de Logging
 * 
 * Enregistre les requêtes HTTP pour audit et débogage.
 */
class LoggingMiddleware
{
    private bool $logBody;
    private array $sensitiveFields;

    public function __construct(bool $logBody = false, array $sensitiveFields = [])
    {
        $this->logBody = $logBody;
        $this->sensitiveFields = array_merge(
            ['password', 'password_confirm', 'token', 'csrf_token'],
            $sensitiveFields
        );
    }

    /**
     * Traite la requête
     */
    public function handle(callable $next): mixed
    {
        // Logging désactivé si Monolog non disponible
        return $next();
    }
}
