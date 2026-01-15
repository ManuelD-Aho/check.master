<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;

/**
 * Middleware CORS (Cross-Origin Resource Sharing)
 * 
 * Gère les en-têtes CORS pour permettre les requêtes cross-origin.
 * Configuration basée sur les paramètres de sécurité.
 */
class CorsMiddleware
{
    /**
     * Origines autorisées par défaut
     */
    private const ALLOWED_ORIGINS = [
        'https://checkmaster.edu',
        'https://admin.checkmaster.edu',
        'http://localhost:8000', // <--- AJOUTEZ CETTE LIGNE
        'http://localhost',      // <--- ET CELLE-CI (pour WAMP classique)
        'http://localhost/check.master',
    ];

    /**
     * Méthodes HTTP autorisées
     */
    private const ALLOWED_METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ];

    /**
     * En-têtes autorisés
     */
    private const ALLOWED_HEADERS = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-CSRF-Token',
        'X-Api-Key',
        'X-Correlation-ID',
        'Accept',
        'Accept-Language',
    ];

    /**
     * En-têtes exposés au client
     */
    private const EXPOSED_HEADERS = [
        'X-Correlation-ID',
        'X-Request-ID',
        'X-Rate-Limit-Remaining',
        'X-Rate-Limit-Reset',
    ];

    /**
     * Durée de cache preflight (en secondes)
     */
    private const MAX_AGE = 86400;

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|mixed Réponse HTTP
     */
    public function handle(callable $next): mixed
    {
        $origin = Request::header('Origin');
        
        // Si pas d'origine, ce n'est pas une requête CORS
        if ($origin === null || $origin === '') {
            return $next();
        }

        // Vérifier si l'origine est autorisée
        if (!$this->isOriginAllowed($origin)) {
            return Response::text('Origin not allowed', 403);
        }

        // Gestion des requêtes preflight (OPTIONS)
        if (Request::method() === 'OPTIONS') {
            return $this->handlePreflight($origin);
        }

        // Exécuter la requête normale
        $response = $next();

        // Ajouter les en-têtes CORS à la réponse
        return $this->addCorsHeaders($response, $origin);
    }

    /**
     * Vérifie si une origine est autorisée
     */
    private function isOriginAllowed(string $origin): bool
    {
        // En mode debug, autoriser localhost
        if (defined('APP_DEBUG') && APP_DEBUG === true) {
            if (str_starts_with($origin, 'http://localhost') ||
                str_starts_with($origin, 'http://127.0.0.1')) {
                return true;
            }
        }

        // Charger les origines depuis la configuration si disponible
        $allowedOrigins = $this->getAllowedOrigins();

        // Vérifier les origines exactes
        if (in_array($origin, $allowedOrigins, true)) {
            return true;
        }

        // Vérifier les patterns avec wildcard
        foreach ($allowedOrigins as $pattern) {
            if (str_contains($pattern, '*')) {
                $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
                if (preg_match($regex, $origin)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retourne les origines autorisées
     *
     * @return array<string>
     */
    private function getAllowedOrigins(): array
    {
        // Charger depuis la config si disponible
        if (function_exists('config')) {
            $configOrigins = config('security.cors.allowed_origins', []);
            if (!empty($configOrigins)) {
                return $configOrigins;
            }
        }

        return self::ALLOWED_ORIGINS;
    }

    /**
     * Gère les requêtes preflight OPTIONS
     */
    private function handlePreflight(string $origin): Response
    {
        $response = new Response('', 204);

        return $response
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', implode(', ', self::ALLOWED_METHODS))
            ->header('Access-Control-Allow-Headers', implode(', ', self::ALLOWED_HEADERS))
            ->header('Access-Control-Max-Age', (string) self::MAX_AGE)
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Vary', 'Origin');
    }

    /**
     * Ajoute les en-têtes CORS à une réponse existante
     */
    private function addCorsHeaders(mixed $response, string $origin): mixed
    {
        if (!$response instanceof Response) {
            return $response;
        }

        return $response
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Expose-Headers', implode(', ', self::EXPOSED_HEADERS))
            ->header('Vary', 'Origin');
    }
}
