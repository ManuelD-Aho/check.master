<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;
use Src\Support\Auth;

/**
 * Middleware Session Expiration
 * 
 * Gère l'expiration des sessions utilisateur et le renouvellement automatique.
 * Affiche un avertissement avant expiration et force la déconnexion si expirée.
 */
class SessionExpirationMiddleware
{
    /**
     * Durée de session par défaut (en secondes) - 30 minutes
     */
    private const DEFAULT_SESSION_LIFETIME = 1800;

    /**
     * Délai d'avertissement avant expiration (en secondes) - 5 minutes
     */
    private const WARNING_THRESHOLD = 300;

    /**
     * Durée maximale d'inactivité (en secondes) - 1 heure
     */
    private const MAX_INACTIVITY = 3600;

    /**
     * Nom du cookie de session
     */
    private const SESSION_COOKIE = 'session_token';

    /**
     * Routes exemptées de la vérification d'expiration
     */
    private const EXEMPT_ROUTES = [
        '/',
        '/login',
        '/logout',
        '/api/session/refresh',
        '/api/session/check',
        '/assets/',
        '/css/',
        '/js/',
    ];

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|mixed Réponse HTTP
     */
    public function handle(callable $next): mixed
    {
        $uri = Request::uri();

        // Vérifier si la route est exemptée
        if ($this->isExemptRoute($uri)) {
            return $next();
        }

        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return $next();
        }

        // Vérifier et gérer l'expiration de session
        $sessionStatus = $this->checkSessionStatus();

        switch ($sessionStatus) {
            case 'expired':
                return $this->handleExpiredSession();
            
            case 'warning':
                // Ajouter un en-tête d'avertissement
                $response = $next();
                return $this->addWarningHeaders($response);
            
            case 'active':
            default:
                // Renouveler le timestamp d'activité
                $this->renewActivity();
                return $next();
        }
    }

    /**
     * Vérifie si la route est exemptée
     */
    private function isExemptRoute(string $uri): bool
    {
        foreach (self::EXEMPT_ROUTES as $exempt) {
            if ($uri === $exempt || str_starts_with($uri, $exempt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie le statut de la session
     *
     * @return string 'active', 'warning', ou 'expired'
     */
    private function checkSessionStatus(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $lastActivity = $_SESSION['last_activity'] ?? null;
        $sessionStart = $_SESSION['session_start'] ?? null;
        
        $now = time();
        $lifetime = $this->getSessionLifetime();

        // Vérifier si pas d'activité enregistrée
        if ($lastActivity === null) {
            $_SESSION['last_activity'] = $now;
            $_SESSION['session_start'] = $now;
            return 'active';
        }

        // Vérifier l'inactivité
        $inactivityDuration = $now - (int) $lastActivity;
        if ($inactivityDuration > self::MAX_INACTIVITY) {
            return 'expired';
        }

        // Vérifier la durée totale de session
        if ($sessionStart !== null) {
            $sessionDuration = $now - (int) $sessionStart;
            if ($sessionDuration > $lifetime) {
                return 'expired';
            }

            // Vérifier si proche de l'expiration
            $timeRemaining = $lifetime - $sessionDuration;
            if ($timeRemaining <= self::WARNING_THRESHOLD) {
                return 'warning';
            }
        }

        return 'active';
    }

    /**
     * Gère une session expirée
     */
    private function handleExpiredSession(): Response
    {
        // Détruire la session
        $this->destroySession();

        // Déconnecter l'utilisateur
        Auth::logout();

        // Requête AJAX : retourner JSON
        if (Request::isAjax()) {
            return Response::text(json_encode([
                'error' => true,
                'code' => 'SESSION_EXPIRED',
                'message' => 'Votre session a expiré. Veuillez vous reconnecter.',
                'redirect' => '/login?expired=1',
            ]), 401)->header('Content-Type', 'application/json');
        }

        // Requête normale : rediriger
        return Response::redirect('/login?expired=1');
    }

    /**
     * Ajoute les en-têtes d'avertissement d'expiration
     */
    private function addWarningHeaders(mixed $response): mixed
    {
        if (!$response instanceof Response) {
            return $response;
        }

        $timeRemaining = $this->getTimeRemaining();

        return $response
            ->header('X-Session-Warning', 'true')
            ->header('X-Session-Expires-In', (string) $timeRemaining)
            ->header('X-Session-Refresh-Url', '/api/session/refresh');
    }

    /**
     * Renouvelle le timestamp d'activité
     */
    private function renewActivity(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Détruit la session courante
     */
    private function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            // Supprimer le cookie de session
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }

        // Supprimer le cookie de session personnalisé
        setcookie(self::SESSION_COOKIE, '', time() - 3600, '/', '', true, true);
    }

    /**
     * Retourne la durée de vie de session configurée
     */
    private function getSessionLifetime(): int
    {
        // Charger depuis la config si disponible
        if (function_exists('config')) {
            return (int) config('session.lifetime', self::DEFAULT_SESSION_LIFETIME);
        }

        return self::DEFAULT_SESSION_LIFETIME;
    }

    /**
     * Retourne le temps restant avant expiration (en secondes)
     */
    private function getTimeRemaining(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionStart = $_SESSION['session_start'] ?? time();
        $lifetime = $this->getSessionLifetime();
        $elapsed = time() - (int) $sessionStart;

        return max(0, $lifetime - $elapsed);
    }

    /**
     * Renouvelle la session (prolonge sa durée de vie)
     */
    public static function refresh(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Auth::check()) {
            return false;
        }

        // Régénérer l'ID de session pour sécurité
        session_regenerate_id(true);

        // Réinitialiser les timestamps
        $_SESSION['last_activity'] = time();
        $_SESSION['session_start'] = time();

        return true;
    }

    /**
     * Vérifie si la session est encore valide
     */
    public static function isValid(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Auth::check()) {
            return false;
        }

        $lastActivity = $_SESSION['last_activity'] ?? null;
        if ($lastActivity === null) {
            return true;
        }

        $inactivityDuration = time() - (int) $lastActivity;
        return $inactivityDuration <= self::MAX_INACTIVITY;
    }

    /**
     * Retourne les informations de la session courante
     *
     * @return array<string, mixed>
     */
    public static function getSessionInfo(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionStart = $_SESSION['session_start'] ?? time();
        $lastActivity = $_SESSION['last_activity'] ?? time();
        $lifetime = (function_exists('config') 
            ? (int) config('session.lifetime', self::DEFAULT_SESSION_LIFETIME) 
            : self::DEFAULT_SESSION_LIFETIME);

        $now = time();
        $elapsed = $now - (int) $sessionStart;
        $remaining = max(0, $lifetime - $elapsed);
        $inactivity = $now - (int) $lastActivity;

        return [
            'started_at' => date('Y-m-d H:i:s', (int) $sessionStart),
            'last_activity' => date('Y-m-d H:i:s', (int) $lastActivity),
            'elapsed_seconds' => $elapsed,
            'remaining_seconds' => $remaining,
            'inactivity_seconds' => $inactivity,
            'is_warning' => $remaining <= self::WARNING_THRESHOLD,
            'is_expired' => $remaining <= 0 || $inactivity > self::MAX_INACTIVITY,
        ];
    }
}
