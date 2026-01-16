<?php

declare(strict_types=1);

namespace Src\RateLimit;

use Src\Exceptions\TooManyRequestsException;
use Src\Support\CacheFactory;

/**
 * Rate Limiter - Contrôle du taux de requêtes
 * 
 * Fonctionnalités:
 * - Limitation par IP, utilisateur, ou clé personnalisée
 * - Algorithmes: Token Bucket, Sliding Window, Fixed Window
 * - Support de multiples limites (par minute, heure, jour)
 * - Whitelist/Blacklist
 * - Headers HTTP standard (X-RateLimit-*)
 * - Persistence via Cache
 * 
 * @package Src\RateLimit
 */
class RateLimiter
{
    private $cache;
    private string $prefix = 'ratelimit:';
    private array $whitelistedIps = [];
    private array $blacklistedIps = [];

    /**
     * Constructeur
     *
     * @param array $config Configuration
     */
    public function __construct(array $config = [])
    {
        $this->cache = CacheFactory::create('redis');
        $this->whitelistedIps = $config['whitelist'] ?? [];
        $this->blacklistedIps = $config['blacklist'] ?? [];
    }

    /**
     * Vérifier et consommer un jeton (Token Bucket)
     *
     * @param string $key Clé unique (IP, user_id, etc.)
     * @param int $maxAttempts Nombre maximum de tentatives
     * @param int $decayMinutes Fenêtre temporelle en minutes
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function attempt(string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        // Vérifier blacklist
        if ($this->isBlacklisted($key)) {
            throw new TooManyRequestsException("IP blacklistée");
        }

        // Vérifier whitelist
        if ($this->isWhitelisted($key)) {
            return true;
        }

        $cacheKey = $this->prefix . $key;
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            // Première requête
            $data = [
                'attempts' => 1,
                'reset_at' => time() + ($decayMinutes * 60)
            ];
            $this->cache->set($cacheKey, $data, $decayMinutes * 60);
            return true;
        }

        // Vérifier si la fenêtre a expiré
        if (time() >= $data['reset_at']) {
            $data = [
                'attempts' => 1,
                'reset_at' => time() + ($decayMinutes * 60)
            ];
            $this->cache->set($cacheKey, $data, $decayMinutes * 60);
            return true;
        }

        // Incrémenter les tentatives
        if ($data['attempts'] < $maxAttempts) {
            $data['attempts']++;
            $ttl = $data['reset_at'] - time();
            $this->cache->set($cacheKey, $data, $ttl);
            return true;
        }

        // Limite atteinte
        $retryAfter = $data['reset_at'] - time();
        throw new TooManyRequestsException(
            "Trop de requêtes. Réessayez dans {$retryAfter} secondes.",
            0,
            null,
            $retryAfter
        );
    }

    /**
     * Vérifier sans consommer (peek)
     *
     * @param string $key Clé
     * @param int $maxAttempts Maximum
     * @return bool True si autorisé
     */
    public function check(string $key, int $maxAttempts = 60): bool
    {
        if ($this->isWhitelisted($key)) {
            return true;
        }

        if ($this->isBlacklisted($key)) {
            return false;
        }

        $cacheKey = $this->prefix . $key;
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            return true;
        }

        if (time() >= $data['reset_at']) {
            return true;
        }

        return $data['attempts'] < $maxAttempts;
    }

    /**
     * Obtenir le nombre de tentatives restantes
     *
     * @param string $key Clé
     * @param int $maxAttempts Maximum
     * @return int Tentatives restantes
     */
    public function remaining(string $key, int $maxAttempts = 60): int
    {
        if ($this->isWhitelisted($key)) {
            return $maxAttempts;
        }

        $cacheKey = $this->prefix . $key;
        $data = $this->cache->get($cacheKey);

        if ($data === null || time() >= $data['reset_at']) {
            return $maxAttempts;
        }

        return max(0, $maxAttempts - $data['attempts']);
    }

    /**
     * Obtenir le temps avant reset (en secondes)
     *
     * @param string $key Clé
     * @return int Secondes avant reset
     */
    public function availableIn(string $key): int
    {
        $cacheKey = $this->prefix . $key;
        $data = $this->cache->get($cacheKey);

        if ($data === null || time() >= $data['reset_at']) {
            return 0;
        }

        return max(0, $data['reset_at'] - time());
    }

    /**
     * Réinitialiser le compteur pour une clé
     *
     * @param string $key Clé
     * @return void
     */
    public function reset(string $key): void
    {
        $cacheKey = $this->prefix . $key;
        $this->cache->delete($cacheKey);
    }

    /**
     * Obtenir les headers HTTP rate limit
     *
     * @param string $key Clé
     * @param int $maxAttempts Maximum
     * @return array Headers
     */
    public function getHeaders(string $key, int $maxAttempts = 60): array
    {
        return [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $this->remaining($key, $maxAttempts),
            'X-RateLimit-Reset' => time() + $this->availableIn($key)
        ];
    }

    /**
     * Sliding Window Rate Limiting (plus précis)
     *
     * @param string $key Clé
     * @param int $maxAttempts Maximum
     * @param int $windowSeconds Fenêtre en secondes
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function slidingWindow(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        if ($this->isWhitelisted($key)) {
            return true;
        }

        if ($this->isBlacklisted($key)) {
            throw new TooManyRequestsException("IP blacklistée");
        }

        $cacheKey = $this->prefix . 'sliding:' . $key;
        $now = microtime(true);
        $windowStart = $now - $windowSeconds;

        // Récupérer les timestamps
        $timestamps = $this->cache->get($cacheKey) ?? [];

        // Filtrer les anciens timestamps
        $timestamps = array_filter($timestamps, fn($ts) => $ts > $windowStart);

        // Vérifier la limite
        if (count($timestamps) >= $maxAttempts) {
            $oldestInWindow = min($timestamps);
            $retryAfter = (int) ceil($oldestInWindow + $windowSeconds - $now);

            throw new TooManyRequestsException(
                "Trop de requêtes. Réessayez dans {$retryAfter} secondes.",
                0,
                null,
                $retryAfter
            );
        }

        // Ajouter le timestamp actuel
        $timestamps[] = $now;
        $this->cache->set($cacheKey, $timestamps, $windowSeconds);

        return true;
    }

    /**
     * Rate limiting par IP
     *
     * @param string $ip Adresse IP
     * @param int $maxAttempts Maximum
     * @param int $decayMinutes Fenêtre
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function forIp(string $ip, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        return $this->attempt('ip:' . $ip, $maxAttempts, $decayMinutes);
    }

    /**
     * Rate limiting par utilisateur
     *
     * @param int $userId ID utilisateur
     * @param int $maxAttempts Maximum
     * @param int $decayMinutes Fenêtre
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function forUser(int $userId, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        return $this->attempt('user:' . $userId, $maxAttempts, $decayMinutes);
    }

    /**
     * Rate limiting pour une action spécifique
     *
     * @param string $action Nom de l'action
     * @param string $identifier Identifiant (IP ou user_id)
     * @param int $maxAttempts Maximum
     * @param int $decayMinutes Fenêtre
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function forAction(
        string $action,
        string $identifier,
        int $maxAttempts,
        int $decayMinutes
    ): bool {
        $key = "action:{$action}:{$identifier}";
        return $this->attempt($key, $maxAttempts, $decayMinutes);
    }

    /**
     * Limiter les tentatives de login
     *
     * @param string $identifier Email ou username
     * @param int $maxAttempts Maximum (défaut: 5)
     * @param int $decayMinutes Fenêtre (défaut: 15)
     * @return bool True si autorisé
     * @throws TooManyRequestsException
     */
    public function forLogin(string $identifier, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        return $this->attempt('login:' . $identifier, $maxAttempts, $decayMinutes);
    }

    /**
     * Vérifier si une IP est whitelistée
     *
     * @param string $key Clé (peut contenir IP)
     * @return bool
     */
    private function isWhitelisted(string $key): bool
    {
        // Extraire IP de la clé si format "ip:x.x.x.x"
        if (strpos($key, 'ip:') === 0) {
            $ip = substr($key, 3);
            return in_array($ip, $this->whitelistedIps);
        }

        return false;
    }

    /**
     * Vérifier si une IP est blacklistée
     *
     * @param string $key Clé
     * @return bool
     */
    private function isBlacklisted(string $key): bool
    {
        if (strpos($key, 'ip:') === 0) {
            $ip = substr($key, 3);
            return in_array($ip, $this->blacklistedIps);
        }

        return false;
    }

    /**
     * Ajouter une IP à la whitelist
     *
     * @param string $ip Adresse IP
     * @return void
     */
    public function addToWhitelist(string $ip): void
    {
        if (!in_array($ip, $this->whitelistedIps)) {
            $this->whitelistedIps[] = $ip;
        }
    }

    /**
     * Ajouter une IP à la blacklist
     *
     * @param string $ip Adresse IP
     * @param int $duration Durée en secondes (0 = permanent)
     * @return void
     */
    public function addToBlacklist(string $ip, int $duration = 0): void
    {
        if (!in_array($ip, $this->blacklistedIps)) {
            $this->blacklistedIps[] = $ip;

            if ($duration > 0) {
                $cacheKey = $this->prefix . 'blacklist:' . $ip;
                $this->cache->set($cacheKey, true, $duration);
            }
        }
    }

    /**
     * Retirer une IP de la blacklist
     *
     * @param string $ip Adresse IP
     * @return void
     */
    public function removeFromBlacklist(string $ip): void
    {
        $this->blacklistedIps = array_filter(
            $this->blacklistedIps,
            fn($blockedIp) => $blockedIp !== $ip
        );

        $cacheKey = $this->prefix . 'blacklist:' . $ip;
        $this->cache->delete($cacheKey);
    }

    /**
     * Obtenir toutes les statistiques
     *
     * @param string $key Clé
     * @return array Statistiques
     */
    public function getStats(string $key): array
    {
        $cacheKey = $this->prefix . $key;
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            return [
                'attempts' => 0,
                'reset_at' => null,
                'is_limited' => false
            ];
        }

        return [
            'attempts' => $data['attempts'],
            'reset_at' => $data['reset_at'],
            'reset_in_seconds' => max(0, $data['reset_at'] - time()),
            'is_limited' => time() < $data['reset_at']
        ];
    }

    /**
     * Nettoyer toutes les données de rate limiting
     *
     * @return void
     */
    public function clear(): void
    {
        // Note: Nécessite support pattern delete dans le cache
        // Pour Redis: KEYS ratelimit:* puis DELETE
        // Implémentation dépend du backend de cache
    }
}
