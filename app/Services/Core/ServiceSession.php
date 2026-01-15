<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\Utilisateur;
use Src\Http\Request;

/**
 * Service Session
 *
 * Gère les sessions utilisateur, cookies, messages flash et redirections post-login.
 * Centralise toute la logique de gestion des sessions pour l'authentification.
 *
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceSession
{
    /**
     * Nom du cookie de session
     */
    private const COOKIE_SESSION = 'session_token';

    /**
     * Durée du cookie en secondes (8 heures)
     */
    private const COOKIE_LIFETIME = 28800;

    /**
     * Clés pour les messages flash
     */
    private const FLASH_ERROR = 'flash_error';
    private const FLASH_SUCCESS = 'flash_success';
    private const FLASH_INFO = 'flash_info';

    /**
     * Clé pour l'URL de redirection post-login
     */
    private const REDIRECT_AFTER_LOGIN = 'redirect_after_login';

    /**
     * Démarre la session PHP si nécessaire
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Crée un cookie de session sécurisé
     */
    public static function setCookie(string $token): void
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        setcookie(
            self::COOKIE_SESSION,
            $token,
            [
                'expires' => time() + self::COOKIE_LIFETIME,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ],
        );
    }

    /**
     * Supprime le cookie de session
     */
    public static function deleteCookie(): void
    {
        setcookie(
            self::COOKIE_SESSION,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ],
        );
    }

    /**
     * Récupère le token depuis le cookie
     */
    public static function getToken(): ?string
    {
        return Request::cookie(self::COOKIE_SESSION);
    }

    /**
     * Définit un message flash d'erreur
     */
    public static function setFlashError(string $message): void
    {
        self::start();
        $_SESSION[self::FLASH_ERROR] = $message;
    }

    /**
     * Définit un message flash de succès
     */
    public static function setFlashSuccess(string $message): void
    {
        self::start();
        $_SESSION[self::FLASH_SUCCESS] = $message;
    }

    /**
     * Définit un message flash d'information
     */
    public static function setFlashInfo(string $message): void
    {
        self::start();
        $_SESSION[self::FLASH_INFO] = $message;
    }

    /**
     * Récupère et supprime un message flash d'erreur
     */
    public static function getFlashError(): ?string
    {
        self::start();
        $message = $_SESSION[self::FLASH_ERROR] ?? null;
        unset($_SESSION[self::FLASH_ERROR]);

        return $message;
    }

    /**
     * Récupère et supprime un message flash de succès
     */
    public static function getFlashSuccess(): ?string
    {
        self::start();
        $message = $_SESSION[self::FLASH_SUCCESS] ?? null;
        unset($_SESSION[self::FLASH_SUCCESS]);

        return $message;
    }

    /**
     * Récupère et supprime un message flash d'information
     */
    public static function getFlashInfo(): ?string
    {
        self::start();
        $message = $_SESSION[self::FLASH_INFO] ?? null;
        unset($_SESSION[self::FLASH_INFO]);

        return $message;
    }

    /**
     * Définit l'URL de redirection après login
     */
    public static function setRedirectAfterLogin(string $url): void
    {
        self::start();
        $_SESSION[self::REDIRECT_AFTER_LOGIN] = $url;
    }

    /**
     * Récupère et supprime l'URL de redirection après login
     */
    public static function getRedirectAfterLogin(string $default = '/dashboard'): string
    {
        self::start();
        $url = $_SESSION[self::REDIRECT_AFTER_LOGIN] ?? $default;
        unset($_SESSION[self::REDIRECT_AFTER_LOGIN]);

        return $url;
    }

    /**
     * Détruit la session complète
     */
    public static function destroy(): void
    {
        self::start();

        // Supprimer le cookie de session
        self::deleteCookie();

        // Vider et détruire la session PHP
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                ],
            );
        }

        session_destroy();
    }

    /**
     * Régénère l'ID de session (protection contre fixation)
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Vérifie si l'utilisateur doit changer son mot de passe
     */
    public static function requiresPasswordChange(Utilisateur $user): bool
    {
        return (bool) $user->doit_changer_mdp;
    }
}
