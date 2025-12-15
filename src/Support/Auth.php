<?php

declare(strict_types=1);

namespace Src\Support;

use App\Models\Utilisateur;
use App\Models\SessionActive;
use App\Services\Security\ServicePermissions;
use Src\Http\Request;

/**
 * Helper d'authentification
 * 
 * Fournit des méthodes statiques pour accéder à l'utilisateur connecté.
 */
class Auth
{
    private static ?Utilisateur $user = null;
    private static ?string $sessionToken = null;
    private static bool $resolved = false;

    /**
     * Initialise l'authentification depuis le cookie/session
     */
    public static function init(): void
    {
        if (self::$resolved) {
            return;
        }

        self::$resolved = true;
        self::$sessionToken = Request::cookie('session_token');

        if (self::$sessionToken === null) {
            return;
        }

        $session = SessionActive::findByToken(self::$sessionToken);
        if ($session === null || !$session->estValide()) {
            self::$user = null;
            return;
        }

        // Mettre à jour dernière activité
        $session->majDerniereActivite();
        $session->save();

        self::$user = $session->getUtilisateur();
    }

    /**
     * Réinitialise l'état (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$user = null;
        self::$sessionToken = null;
        self::$resolved = false;
    }

    /**
     * Retourne l'ID de l'utilisateur connecté
     */
    public static function id(): ?int
    {
        self::init();
        return self::$user?->getId();
    }

    /**
     * Retourne l'utilisateur connecté
     */
    public static function user(): ?Utilisateur
    {
        self::init();
        return self::$user;
    }

    /**
     * Vérifie si un utilisateur est connecté
     */
    public static function check(): bool
    {
        self::init();
        return self::$user !== null;
    }

    /**
     * Vérifie si l'utilisateur est un invité (non connecté)
     */
    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Vérifie si l'utilisateur a une permission
     */
    public static function hasPermission(string $ressource, string $action): bool
    {
        $userId = self::id();
        if ($userId === null) {
            return false;
        }

        return ServicePermissions::verifier($userId, $ressource, $action);
    }

    /**
     * Définit l'utilisateur manuellement (après login)
     */
    public static function setUser(Utilisateur $user, string $token): void
    {
        self::$user = $user;
        self::$sessionToken = $token;
        self::$resolved = true;
    }

    /**
     * Déconnecte l'utilisateur
     */
    public static function logout(): void
    {
        self::$user = null;
        self::$sessionToken = null;
        self::$resolved = true;
    }

    /**
     * Retourne le token de session actuel
     */
    public static function token(): ?string
    {
        self::init();
        return self::$sessionToken;
    }
}
