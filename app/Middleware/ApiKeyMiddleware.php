<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use App\Services\Core\ServiceAudit;

/**
 * Middleware API Key
 * 
 * Valide les clés API pour les requêtes d'intégration externe.
 * Supporte plusieurs types de clés avec des permissions différentes.
 */
class ApiKeyMiddleware
{
    /**
     * Nom de l'en-tête pour la clé API
     */
    public const HEADER_NAME = 'X-Api-Key';

    /**
     * Nom alternatif (Authorization Bearer)
     */
    public const AUTH_HEADER = 'Authorization';

    /**
     * Préfixe pour les clés API
     */
    private const KEY_PREFIX = 'cm_';

    /**
     * Routes qui nécessitent une clé API
     */
    private const API_ROUTES = [
        '/api/',
        '/webhook/',
        '/integration/',
    ];

    /**
     * Routes exemptées de la validation API Key
     */
    private const EXEMPT_ROUTES = [
        '/api/health',
        '/api/status',
        '/api/docs',
    ];

    /**
     * Informations sur la clé API validée
     *
     * @var array<string, mixed>|null
     */
    private static ?array $apiKeyInfo = null;

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|mixed Réponse HTTP
     */
    public function handle(callable $next): mixed
    {
        $uri = Request::uri();

        // Vérifier si la route nécessite une clé API
        if (!$this->requiresApiKey($uri)) {
            return $next();
        }

        // Extraire la clé API
        $apiKey = $this->extractApiKey();

        if ($apiKey === null) {
            return $this->unauthorizedResponse('Clé API requise');
        }

        // Valider la clé API
        $keyInfo = $this->validateApiKey($apiKey);

        if ($keyInfo === null) {
            // Logger la tentative d'accès invalide
            $this->logInvalidAttempt($apiKey);
            return $this->unauthorizedResponse('Clé API invalide');
        }

        // Vérifier si la clé est active
        if (!($keyInfo['active'] ?? false)) {
            return $this->unauthorizedResponse('Clé API désactivée');
        }

        // Vérifier l'expiration
        if (isset($keyInfo['expires_at']) && strtotime($keyInfo['expires_at']) < time()) {
            return $this->unauthorizedResponse('Clé API expirée');
        }

        // Vérifier les permissions de la clé pour cette route
        if (!$this->hasRoutePermission($keyInfo, $uri)) {
            return $this->forbiddenResponse('Accès non autorisé pour cette ressource');
        }

        // Stocker les informations de la clé pour usage ultérieur
        self::$apiKeyInfo = $keyInfo;
        $GLOBALS['api_key_info'] = $keyInfo;

        // Mettre à jour la dernière utilisation
        $this->updateLastUsed($keyInfo['id'] ?? 0);

        return $next();
    }

    /**
     * Vérifie si la route nécessite une clé API
     */
    private function requiresApiKey(string $uri): bool
    {
        // Vérifier les routes exemptées
        foreach (self::EXEMPT_ROUTES as $exempt) {
            if (str_starts_with($uri, $exempt)) {
                return false;
            }
        }

        // Vérifier si c'est une route API
        foreach (self::API_ROUTES as $apiRoute) {
            if (str_starts_with($uri, $apiRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extrait la clé API des en-têtes
     */
    private function extractApiKey(): ?string
    {
        // Essayer l'en-tête X-Api-Key
        $apiKey = Request::header(self::HEADER_NAME);
        if ($apiKey !== null && $apiKey !== '') {
            return $apiKey;
        }

        // Essayer l'en-tête Authorization (Bearer token)
        $authHeader = Request::header(self::AUTH_HEADER);
        if ($authHeader !== null && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            // Vérifier si c'est une clé API (commence par le préfixe)
            if (str_starts_with($token, self::KEY_PREFIX)) {
                return $token;
            }
        }

        // Essayer le paramètre de requête (moins sécurisé, pour debug uniquement)
        if (defined('APP_DEBUG') && APP_DEBUG === true) {
            return Request::get('api_key');
        }

        return null;
    }

    /**
     * Valide une clé API et retourne ses informations
     *
     * @return array<string, mixed>|null
     */
    private function validateApiKey(string $apiKey): ?array
    {
        // Valider le format de base
        if (!str_starts_with($apiKey, self::KEY_PREFIX)) {
            return null;
        }

        // Hasher la clé pour comparaison en base
        $hashedKey = hash('sha256', $apiKey);

        // Rechercher en base de données
        try {
            $pdo = $this->getPdo();
            if ($pdo === null) {
                return null;
            }

            $stmt = $pdo->prepare(
                'SELECT id_api_key as id, nom, type_cle, permissions, ip_whitelist, 
                        rate_limit, actif as active, date_expiration as expires_at,
                        derniere_utilisation as last_used
                 FROM api_keys 
                 WHERE cle_hash = :hash'
            );
            $stmt->execute(['hash' => $hashedKey]);
            
            /** @var array<string, mixed>|false $result */
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result === false) {
                return null;
            }

            // Décoder les permissions JSON si présentes
            if (isset($result['permissions']) && is_string($result['permissions'])) {
                $result['permissions'] = json_decode($result['permissions'], true) ?? [];
            }

            // Décoder la whitelist IP si présente
            if (isset($result['ip_whitelist']) && is_string($result['ip_whitelist'])) {
                $result['ip_whitelist'] = json_decode($result['ip_whitelist'], true) ?? [];
            }

            // Vérifier la whitelist IP si configurée
            if (!empty($result['ip_whitelist'])) {
                $clientIp = Request::ip();
                if (!in_array($clientIp, $result['ip_whitelist'], true)) {
                    return null;
                }
            }

            return $result;
        } catch (\Exception $e) {
            // Logger l'erreur mais ne pas exposer les détails
            if (function_exists('logger')) {
                logger()->error('Erreur validation API key', ['exception' => $e->getMessage()]);
            }
            return null;
        }
    }

    /**
     * Vérifie si la clé a la permission pour la route
     *
     * @param array<string, mixed> $keyInfo
     */
    private function hasRoutePermission(array $keyInfo, string $uri): bool
    {
        $permissions = $keyInfo['permissions'] ?? [];
        
        // Si pas de permissions définies, autoriser tout
        if (empty($permissions)) {
            return true;
        }

        // Vérifier les routes autorisées
        $allowedRoutes = $permissions['routes'] ?? [];
        foreach ($allowedRoutes as $pattern) {
            if ($this->matchRoutePattern($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compare une URI avec un pattern de route
     */
    private function matchRoutePattern(string $uri, string $pattern): bool
    {
        // Pattern exact
        if ($uri === $pattern) {
            return true;
        }

        // Pattern avec wildcard
        if (str_contains($pattern, '*')) {
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
            return (bool) preg_match($regex, $uri);
        }

        // Pattern de préfixe (se termine par /)
        if (str_ends_with($pattern, '/')) {
            return str_starts_with($uri, $pattern);
        }

        return false;
    }

    /**
     * Met à jour la dernière utilisation de la clé
     */
    private function updateLastUsed(int $keyId): void
    {
        try {
            $pdo = $this->getPdo();
            if ($pdo !== null && $keyId > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE api_keys SET derniere_utilisation = NOW() WHERE id_api_key = :id'
                );
                $stmt->execute(['id' => $keyId]);
            }
        } catch (\Exception $e) {
            // Silencieux - mise à jour non critique
        }
    }

    /**
     * Journalise une tentative d'accès avec une clé invalide
     */
    private function logInvalidAttempt(string $apiKey): void
    {
        try {
            // Masquer partiellement la clé pour le log
            $maskedKey = substr($apiKey, 0, 8) . '...' . substr($apiKey, -4);
            
            if (class_exists(ServiceAudit::class)) {
                ServiceAudit::log(
                    'Tentative accès API avec clé invalide',
                    'api_key',
                    0,
                    [
                        'masked_key' => $maskedKey,
                        'ip' => Request::ip(),
                        'uri' => Request::uri(),
                        'user_agent' => Request::header('User-Agent'),
                    ]
                );
            }
        } catch (\Exception $e) {
            // Silencieux
        }
    }

    /**
     * Retourne une réponse 401 Unauthorized
     */
    private function unauthorizedResponse(string $message): JsonResponse
    {
        return JsonResponse::unauthorized($message);
    }

    /**
     * Retourne une réponse 403 Forbidden
     */
    private function forbiddenResponse(string $message): JsonResponse
    {
        return JsonResponse::forbidden($message);
    }

    /**
     * Retourne la connexion PDO
     */
    private function getPdo(): ?\PDO
    {
        if (function_exists('db')) {
            return db();
        }

        // Fallback si helper non disponible
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof \PDO) {
            return $GLOBALS['pdo'];
        }

        return null;
    }

    /**
     * Retourne les informations de la clé API validée
     *
     * @return array<string, mixed>|null
     */
    public static function getApiKeyInfo(): ?array
    {
        return self::$apiKeyInfo;
    }

    /**
     * Vérifie si la requête est authentifiée via API Key
     */
    public static function isAuthenticated(): bool
    {
        return self::$apiKeyInfo !== null;
    }

    /**
     * Retourne le type de la clé API
     */
    public static function getKeyType(): ?string
    {
        return self::$apiKeyInfo['type_cle'] ?? null;
    }

    /**
     * Réinitialise l'état (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$apiKeyInfo = null;
        unset($GLOBALS['api_key_info']);
    }
}
