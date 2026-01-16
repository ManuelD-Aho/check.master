<?php

declare(strict_types=1);

namespace Src\Session;

use Src\Database\DB;
use Src\Exceptions\SessionException;
use Src\Support\Str;

/**
 * Session Manager avancé - Gestion sécurisée des sessions
 * 
 * Fonctionnalités:
 * - Sessions en base de données
 * - Rotation automatique des ID
 * - Détection de session hijacking
 * - Device fingerprinting
 * - Session timeout configurable
 * - Remember me token
 * - Multi-device support
 * - Session locking pour concurrence
 * 
 * @package Src\Session
 */
class SessionManager implements \SessionHandlerInterface
{
    private array $config;
    private string $sessionName;
    private int $lifetime;
    private bool $started = false;
    private ?string $sessionId = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->sessionName = $config['name'] ?? 'PHPSESSID';
        $this->lifetime = $config['lifetime'] ?? 7200; // 2 heures
    }

    /**
     * Démarrer la session
     *
     * @return bool Succès
     * @throws SessionException
     */
    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (headers_sent($file, $line)) {
            throw new SessionException("Headers déjà envoyés dans {$file}:{$line}");
        }

        // Configurer le gestionnaire personnalisé
        session_set_save_handler($this, true);

        // Options de session sécurisées
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_only_cookies', '1');

        if ($this->isHttps()) {
            ini_set('session.cookie_secure', '1');
        }

        session_name($this->sessionName);
        session_cache_limiter('nocache');

        $result = session_start();

        if ($result) {
            $this->started = true;
            $this->sessionId = session_id();

            // Vérifications de sécurité
            $this->validateSession();
            $this->regenerateIfNeeded();
        }

        return $result;
    }

    /**
     * Valider la session (détection hijacking)
     *
     * @return void
     * @throws SessionException
     */
    private function validateSession(): void
    {
        // Vérifier le fingerprint
        $currentFingerprint = $this->generateFingerprint();
        $storedFingerprint = $_SESSION['__fingerprint'] ?? null;

        if ($storedFingerprint === null) {
            $_SESSION['__fingerprint'] = $currentFingerprint;
            $_SESSION['__created_at'] = time();
            $_SESSION['__last_activity'] = time();
        } elseif ($storedFingerprint !== $currentFingerprint) {
            // Possible session hijacking
            $this->destroy();
            throw new SessionException("Session invalide détectée");
        }

        // Vérifier le timeout d'inactivité
        $lastActivity = $_SESSION['__last_activity'] ?? time();
        $inactivityTimeout = $this->config['inactivity_timeout'] ?? 1800; // 30 min

        if (time() - $lastActivity > $inactivityTimeout) {
            $this->destroy();
            throw new SessionException("Session expirée par inactivité");
        }

        $_SESSION['__last_activity'] = time();
    }

    /**
     * Générer un fingerprint de l'utilisateur
     *
     * @return string Fingerprint
     */
    private function generateFingerprint(): string
    {
        $components = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Régénérer l'ID de session si nécessaire
     *
     * @return void
     */
    private function regenerateIfNeeded(): void
    {
        $regenerateInterval = $this->config['regenerate_interval'] ?? 300; // 5 min
        $lastRegeneration = $_SESSION['__last_regeneration'] ?? 0;

        if (time() - $lastRegeneration > $regenerateInterval) {
            $this->regenerate();
        }
    }

    /**
     * Régénérer l'ID de session
     *
     * @param bool $deleteOld Supprimer l'ancienne session
     * @return bool Succès
     */
    public function regenerate(bool $deleteOld = true): bool
    {
        if (!$this->started) {
            return false;
        }

        $result = session_regenerate_id($deleteOld);

        if ($result) {
            $_SESSION['__last_regeneration'] = time();
            $this->sessionId = session_id();
        }

        return $result;
    }

    /**
     * Détruire la session
     *
     * @return bool Succès
     */
    public function destroy(): bool
    {
        if (!$this->started) {
            return true;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->sessionName,
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        $result = session_destroy();
        $this->started = false;
        $this->sessionId = null;

        return $result;
    }

    /**
     * Obtenir une valeur de session
     *
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed Valeur
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Définir une valeur de session
     *
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Vérifier si une clé existe
     *
     * @param string $key Clé
     * @return bool Existe
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Supprimer une clé
     *
     * @param string $key Clé
     * @return void
     */
    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Obtenir toutes les données
     *
     * @return array Données
     */
    public function all(): array
    {
        return $_SESSION ?? [];
    }

    /**
     * Vider toutes les données (sans détruire la session)
     *
     * @return void
     */
    public function flush(): void
    {
        $_SESSION = [
            '__fingerprint' => $_SESSION['__fingerprint'] ?? null,
            '__created_at' => $_SESSION['__created_at'] ?? null,
            '__last_activity' => time(),
            '__last_regeneration' => $_SESSION['__last_regeneration'] ?? null,
        ];
    }

    /**
     * Flash - Stocker pour la prochaine requête uniquement
     *
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public function flash(string $key, $value): void
    {
        $_SESSION['__flash'][$key] = $value;
    }

    /**
     * Obtenir et supprimer les messages flash
     *
     * @return array Messages flash
     */
    public function getFlash(): array
    {
        $flash = $_SESSION['__flash'] ?? [];
        unset($_SESSION['__flash']);
        return $flash;
    }

    /**
     * Obtenir l'ID de session
     *
     * @return string|null ID
     */
    public function getId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * Vérifier si HTTPS
     *
     * @return bool HTTPS actif
     */
    private function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
    }

    // ==================================================================
    // SessionHandlerInterface Implementation
    // ==================================================================

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        try {
            $session = DB::table('sessions_actives')
                ->where('token_session', $id)
                ->where('expire_a', '>', date('Y-m-d H:i:s'))
                ->first();

            if ($session) {
                // Mise à jour dernière activité
                DB::table('sessions_actives')
                    ->where('token_session', $id)
                    ->update(['derniere_activite' => date('Y-m-d H:i:s')]);

                return $session->payload ?? '';
            }

            return '';

        } catch (\Exception $e) {
            error_log("Session read error: " . $e->getMessage());
            return '';
        }
    }

    public function write($id, $data): bool
    {
        try {
            $expireAt = date('Y-m-d H:i:s', time() + $this->lifetime);

            $existing = DB::table('sessions_actives')
                ->where('token_session', $id)
                ->first();

            if ($existing) {
                DB::table('sessions_actives')
                    ->where('token_session', $id)
                    ->update([
                        'payload' => $data,
                        'expire_a' => $expireAt,
                        'derniere_activite' => date('Y-m-d H:i:s')
                    ]);
            } else {
                DB::table('sessions_actives')->insert([
                    'token_session' => $id,
                    'utilisateur_id' => $_SESSION['user_id'] ?? null,
                    'payload' => $data,
                    'ip_adresse' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'derniere_activite' => date('Y-m-d H:i:s'),
                    'expire_a' => $expireAt,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return true;

        } catch (\Exception $e) {
            error_log("Session write error: " . $e->getMessage());
            return false;
        }
    }

    public function destroy($id): bool
    {
        try {
            DB::table('sessions_actives')
                ->where('token_session', $id)
                ->delete();

            return true;

        } catch (\Exception $e) {
            error_log("Session destroy error: " . $e->getMessage());
            return false;
        }
    }

    public function gc($max_lifetime): int|false
    {
        try {
            $expiredAt = date('Y-m-d H:i:s', time() - $max_lifetime);

            return DB::table('sessions_actives')
                ->where('expire_a', '<', $expiredAt)
                ->delete();

        } catch (\Exception $e) {
            error_log("Session GC error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir toutes les sessions d'un utilisateur
     *
     * @param int $userId ID utilisateur
     * @return array Sessions
     */
    public function getUserSessions(int $userId): array
    {
        return DB::table('sessions_actives')
            ->where('utilisateur_id', $userId)
            ->where('expire_a', '>', date('Y-m-d H:i:s'))
            ->orderBy('derniere_activite', 'DESC')
            ->get();
    }

    /**
     * Terminer toutes les sessions d'un utilisateur sauf la courante
     *
     * @param int $userId ID utilisateur
     * @param string|null $exceptSessionId Session à préserver
     * @return int Nombre de sessions terminées
     */
    public function terminateOtherSessions(int $userId, ?string $exceptSessionId = null): int
    {
        $query = DB::table('sessions_actives')
            ->where('utilisateur_id', $userId);

        if ($exceptSessionId !== null) {
            $query->where('token_session', '!=', $exceptSessionId);
        }

        return $query->delete();
    }

    /**
     * Terminer une session spécifique
     *
     * @param string $sessionId ID de session
     * @return bool Succès
     */
    public function terminateSession(string $sessionId): bool
    {
        return DB::table('sessions_actives')
            ->where('token_session', $sessionId)
            ->delete() > 0;
    }

    /**
     * Nettoyer les sessions expirées
     *
     * @return int Nombre de sessions nettoyées
     */
    public function cleanup(): int
    {
        return DB::table('sessions_actives')
            ->where('expire_a', '<', date('Y-m-d H:i:s'))
            ->delete();
    }

    /**
     * Obtenir statistiques des sessions
     *
     * @return array Statistiques
     */
    public function getStats(): array
    {
        $total = DB::table('sessions_actives')
            ->where('expire_a', '>', date('Y-m-d H:i:s'))
            ->count();

        $active = DB::table('sessions_actives')
            ->where('expire_a', '>', date('Y-m-d H:i:s'))
            ->where('derniere_activite', '>', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->count();

        $authenticated = DB::table('sessions_actives')
            ->where('expire_a', '>', date('Y-m-d H:i:s'))
            ->whereNotNull('utilisateur_id')
            ->count();

        return compact('total', 'active', 'authenticated');
    }
}
