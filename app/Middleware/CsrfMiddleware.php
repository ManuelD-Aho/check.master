<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\JsonResponse;
use Src\Http\Response;
use Src\Support\CSRF;

/**
 * Middleware CSRF
 * 
 * Vérifie la présence et validité du token CSRF sur les requêtes POST.
 * Protège contre les attaques Cross-Site Request Forgery.
 * 
 * @see Constitution III - Sécurité Par Défaut
 */
class CSRFMiddleware
{
    /**
     * Méthodes HTTP qui nécessitent une vérification CSRF
     */
    private const METHODES_PROTEGEES = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes exclues de la vérification CSRF (ex: webhooks)
     */
    private const ROUTES_EXCLUES = [
        '/api/webhooks/',
    ];

    /**
     * Exécute le middleware
     */
    public function handle(callable $next): mixed
    {
        $method = Request::method();

        // Vérifier seulement les méthodes protégées
        if (!in_array($method, self::METHODES_PROTEGEES, true)) {
            return $next();
        }

        // Vérifier si la route est exclue
        $uri = Request::uri();
        foreach (self::ROUTES_EXCLUES as $routeExclue) {
            if (str_starts_with($uri, $routeExclue)) {
                return $next();
            }
        }

        // Vérifier le token CSRF
        if (!CSRF::check()) {
            // Requête AJAX → réponse JSON
            if (Request::isAjax()) {
                return JsonResponse::error(
                    'Token CSRF invalide ou expiré',
                    'CSRF_ERROR',
                    403
                );
            }

            // Requête normale → redirection avec message
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['flash_error'] = 'Session expirée. Veuillez réessayer.';

            // Rediriger vers la page précédente ou accueil
            $referer = Request::header('Referer') ?? '/';
            return Response::redirect($referer);
        }

        // Régénérer le token après utilisation réussie
        CSRF::regenerate();

        return $next();
    }

    /**
     * Vérifie manuellement le token CSRF
     */
    public static function verify(): bool
    {
        return CSRF::check();
    }
}
