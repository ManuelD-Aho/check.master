<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;
use Src\Support\Auth;

/**
 * Middleware d'Authentification
 * 
 * Vérifie que l'utilisateur est connecté avec une session valide.
 * Redirige vers la page de connexion si non authentifié.
 */
class AuthMiddleware
{
    /**
     * Routes exclues de la vérification d'authentification
     */
    private const ROUTES_PUBLIQUES = [
        '/',
        '/login',
        '/logout',
    ];

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|null Null si autorisé, Response si redirection
     */
    public function handle(callable $next): mixed
    {
        $uri = Request::uri();

        // Vérifier si la route est publique
        if ($this->estRoutePublique($uri)) {
            return $next();
        }

        // Vérifier l'authentification
        if (!Auth::check()) {
            // Stocker l'URL demandée pour redirection après login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['redirect_after_login'] = Request::fullUrl();

            return Response::redirect('/');
        }

        // Vérifier que le compte est toujours actif
        $user = Auth::user();
        if ($user !== null && !$user->estActif()) {
            Auth::logout();
            return Response::redirect('/?error=compte_inactif');
        }

        // Vérifier si l'utilisateur doit changer son mot de passe
        if ($user !== null && $user->doitChangerMotDePasse()) {
            $changePasswordUri = '/change-password';
            if ($uri !== $changePasswordUri) {
                return Response::redirect($changePasswordUri);
            }
        }

        return $next();
    }

    /**
     * Vérifie si une route est publique
     */
    private function estRoutePublique(string $uri): bool
    {
        // Normaliser l'URI
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        // Vérifier les routes exactes
        if (in_array($uri, self::ROUTES_PUBLIQUES, true)) {
            return true;
        }

        // Vérifier les routes partielles (assets, etc.)
        $prefixesPublics = ['/assets/', '/css/', '/js/', '/images/'];
        foreach ($prefixesPublics as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie seulement l'authentification sans gérer la redirection
     */
    public static function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Retourne l'utilisateur connecté ou null
     */
    public static function getUser(): ?\App\Models\Utilisateur
    {
        return Auth::user();
    }
}
