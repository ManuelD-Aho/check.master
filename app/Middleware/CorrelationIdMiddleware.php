<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;

/**
 * Middleware Correlation ID
 * 
 * Génère ou propage un identifiant de corrélation unique pour chaque requête.
 * Permet la traçabilité des requêtes à travers les logs et services.
 */
class CorrelationIdMiddleware
{
    /**
     * Nom de l'en-tête HTTP pour le Correlation ID
     */
    public const HEADER_NAME = 'X-Correlation-ID';

    /**
     * Nom de l'en-tête alternatif (Request ID)
     */
    public const REQUEST_ID_HEADER = 'X-Request-ID';

    /**
     * Préfixe pour les IDs générés
     */
    private const ID_PREFIX = 'cm';

    /**
     * Correlation ID de la requête courante
     */
    private static ?string $correlationId = null;

    /**
     * Request ID unique (différent du correlation ID propagé)
     */
    private static ?string $requestId = null;

    /**
     * Timestamp de début de requête
     */
    private static ?float $startTime = null;

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|mixed Réponse HTTP
     */
    public function handle(callable $next): mixed
    {
        // Enregistrer le temps de début
        self::$startTime = microtime(true);

        // Récupérer ou générer le Correlation ID
        self::$correlationId = $this->resolveCorrelationId();
        
        // Générer un Request ID unique pour cette requête
        self::$requestId = $this->generateRequestId();

        // Stocker dans les globales pour accès facile
        $GLOBALS['correlation_id'] = self::$correlationId;
        $GLOBALS['request_id'] = self::$requestId;

        // Exécuter la requête
        $response = $next();

        // Ajouter les en-têtes à la réponse
        return $this->addHeaders($response);
    }

    /**
     * Résout le Correlation ID depuis les en-têtes ou en génère un nouveau
     */
    private function resolveCorrelationId(): string
    {
        // Chercher dans les en-têtes entrants
        $existingId = Request::header(self::HEADER_NAME);
        
        if ($existingId !== null && $this->isValidCorrelationId($existingId)) {
            return $existingId;
        }

        // Générer un nouveau Correlation ID
        return $this->generateCorrelationId();
    }

    /**
     * Génère un nouveau Correlation ID
     * Format: cm-{timestamp}-{random}
     */
    private function generateCorrelationId(): string
    {
        $timestamp = dechex((int) (microtime(true) * 1000));
        $random = bin2hex(random_bytes(8));
        
        return sprintf('%s-%s-%s', self::ID_PREFIX, $timestamp, $random);
    }

    /**
     * Génère un Request ID unique
     * Format: req-{random}
     */
    private function generateRequestId(): string
    {
        return 'req-' . bin2hex(random_bytes(12));
    }

    /**
     * Valide le format d'un Correlation ID
     */
    private function isValidCorrelationId(string $id): bool
    {
        // Longueur raisonnable (entre 10 et 128 caractères)
        $length = strlen($id);
        if ($length < 10 || $length > 128) {
            return false;
        }

        // Caractères alphanumériques et tirets uniquement
        return (bool) preg_match('/^[a-zA-Z0-9\-_]+$/', $id);
    }

    /**
     * Ajoute les en-têtes de corrélation à la réponse
     */
    private function addHeaders(mixed $response): mixed
    {
        if (!$response instanceof Response) {
            return $response;
        }

        // Calculer le temps de traitement
        $processingTime = self::$startTime !== null 
            ? round((microtime(true) - self::$startTime) * 1000, 2) 
            : 0;

        return $response
            ->header(self::HEADER_NAME, self::$correlationId ?? '')
            ->header(self::REQUEST_ID_HEADER, self::$requestId ?? '')
            ->header('X-Processing-Time', $processingTime . 'ms');
    }

    /**
     * Retourne le Correlation ID de la requête courante
     */
    public static function getCorrelationId(): ?string
    {
        return self::$correlationId;
    }

    /**
     * Retourne le Request ID de la requête courante
     */
    public static function getRequestId(): ?string
    {
        return self::$requestId;
    }

    /**
     * Retourne le temps écoulé depuis le début de la requête (en ms)
     */
    public static function getElapsedTime(): float
    {
        if (self::$startTime === null) {
            return 0.0;
        }

        return round((microtime(true) - self::$startTime) * 1000, 2);
    }

    /**
     * Réinitialise l'état (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$correlationId = null;
        self::$requestId = null;
        self::$startTime = null;
        unset($GLOBALS['correlation_id'], $GLOBALS['request_id']);
    }

    /**
     * Définit manuellement le Correlation ID (pour propagation inter-services)
     */
    public static function setCorrelationId(string $id): void
    {
        self::$correlationId = $id;
        $GLOBALS['correlation_id'] = $id;
    }

    /**
     * Retourne un contexte de log avec les IDs de corrélation
     *
     * @return array<string, string|null>
     */
    public static function getLogContext(): array
    {
        return [
            'correlation_id' => self::$correlationId,
            'request_id' => self::$requestId,
        ];
    }
}
