<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe JsonResponse pour les réponses API JSON
 * 
 * Format standard :
 * - Succès: {"success": true, "data": {...}, "message": "..."}
 * - Erreur: {"error": true, "code": "...", "message": "...", "details": [...]}
 */
class JsonResponse extends Response
{
    public function __construct(array $data = [], int $statusCode = 200)
    {
        parent::__construct(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            $statusCode,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /**
     * Crée une réponse de succès
     */
    public static function success(mixed $data = null, string $message = ''): self
    {
        $response = [
            'success' => true,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== '') {
            $response['message'] = $message;
        }

        return new self($response, 200);
    }

    /**
     * Crée une réponse d'erreur
     */
    public static function error(
        string $message,
        string $code = 'ERROR',
        int $statusCode = 400,
        array $details = []
    ): self {
        $response = [
            'error' => true,
            'code' => $code,
            'message' => $message,
        ];

        if (!empty($details)) {
            $response['details'] = $details;
        }

        return new self($response, $statusCode);
    }

    /**
     * Erreur de validation
     */
    public static function validationError(array $errors): self
    {
        return self::error(
            'Erreurs de validation',
            'VALIDATION_ERROR',
            422,
            $errors
        );
    }

    /**
     * Erreur non autorisé
     */
    public static function unauthorized(string $message = 'Non autorisé'): self
    {
        return self::error($message, 'UNAUTHORIZED', 401);
    }

    /**
     * Erreur interdit
     */
    public static function forbidden(string $message = 'Accès interdit'): self
    {
        return self::error($message, 'FORBIDDEN', 403);
    }

    /**
     * Erreur non trouvé
     */
    public static function notFound(string $message = 'Ressource non trouvée'): self
    {
        return self::error($message, 'NOT_FOUND', 404);
    }

    /**
     * Erreur serveur
     */
    public static function serverError(string $message = 'Erreur serveur interne'): self
    {
        return self::error($message, 'SERVER_ERROR', 500);
    }

    /**
     * Trop de requêtes (rate limit)
     */
    public static function tooManyRequests(string $message = 'Trop de requêtes'): self
    {
        return self::error($message, 'TOO_MANY_REQUESTS', 429);
    }
}
