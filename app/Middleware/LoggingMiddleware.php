<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Support\Auth;
use Src\Support\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Middleware de Logging
 * 
 * Enregistre les requêtes HTTP pour audit et débogage.
 */
class LoggingMiddleware
{
    private LoggerInterface $logger;
    private bool $logBody;
    private array $sensitiveFields;

    public function __construct(bool $logBody = false, array $sensitiveFields = [])
    {
        $this->logger = LoggerFactory::get('request');
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
        $startTime = microtime(true);
        $requestId = $this->generateRequestId();

        // Log début de requête
        $this->logRequest($requestId);

        try {
            // Exécuter la requête
            $response = $next();

            // Log fin de requête
            $this->logResponse($requestId, $startTime);

            return $response;
        } catch (\Throwable $e) {
            // Log erreur
            $this->logError($requestId, $e, $startTime);
            throw $e;
        }
    }

    /**
     * Log la requête entrante
     */
    private function logRequest(string $requestId): void
    {
        $context = [
            'request_id' => $requestId,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'uri' => $_SERVER['REQUEST_URI'] ?? '/',
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'user_id' => Auth::id(),
        ];

        if ($this->logBody && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = $this->sanitizeBody($_POST);
            $context['body'] = $body;
        }

        $this->logger->info('Request started', $context);
    }

    /**
     * Log la réponse
     */
    private function logResponse(string $requestId, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->logger->info('Request completed', [
            'request_id' => $requestId,
            'duration_ms' => $duration,
            'status' => http_response_code(),
            'memory_peak' => round(memory_get_peak_usage(true) / 1048576, 2) . ' MB',
        ]);
    }

    /**
     * Log une erreur
     */
    private function logError(string $requestId, \Throwable $e, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->logger->error('Request failed', [
            'request_id' => $requestId,
            'duration_ms' => $duration,
            'error_class' => get_class($e),
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
        ]);
    }

    /**
     * Génère un ID de requête unique
     */
    private function generateRequestId(): string
    {
        return substr(bin2hex(random_bytes(8)), 0, 16);
    }

    /**
     * Récupère l'IP du client
     */
    private function getClientIp(): string
    {
        $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                return explode(',', $_SERVER[$header])[0];
            }
        }
        return 'unknown';
    }

    /**
     * Masque les champs sensibles
     */
    private function sanitizeBody(array $data): array
    {
        foreach ($this->sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }
        return $data;
    }
}
