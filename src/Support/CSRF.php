<?php

declare(strict_types=1);

namespace Src\Support;

use Src\Http\Request;

/**
 * Gestion des tokens CSRF
 * 
 * Protection contre les attaques Cross-Site Request Forgery.
 */
class CSRF
{
    private const SESSION_KEY = '_csrf_token';
    private const FORM_FIELD = '_csrf_token';
    private const TOKEN_LENGTH = 64;

    /**
     * Génère un nouveau token CSRF
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH / 2));

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    /**
     * Retourne le token actuel ou en génère un nouveau
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            return self::generate();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Vérifie si un token CSRF est valide
     */
    public static function verify(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';

        if ($sessionToken === '') {
            return false;
        }

        // Utiliser hash_equals pour éviter les timing attacks
        return hash_equals($sessionToken, $token);
    }

    /**
     * Vérifie le token depuis la requête
     */
    public static function check(): bool
    {
        $token = Request::post(self::FORM_FIELD) ?? Request::header('X-CSRF-Token');
        return self::verify($token);
    }

    /**
     * Retourne un champ input hidden pour le formulaire
     */
    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="' . self::FORM_FIELD . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Retourne un meta tag pour usage JavaScript
     */
    public static function meta(): string
    {
        $token = self::token();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Régénère le token (après soumission réussie)
     */
    public static function regenerate(): string
    {
        return self::generate();
    }
}
