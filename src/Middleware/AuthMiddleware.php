<?php

declare(strict_types=1);

namespace Src\Middleware;

use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Auth Middleware - Vérification authentification
 * 
 * @package Src\Middleware
 */
class AuthMiddleware
{
    /**
     * Gérer la requête
     *
     * @param Request $request Requête
     * @param callable $next Suivant
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        if (!Auth::check()) {
            return JsonResponse::error('Non authentifié', 401);
        }

        return $next($request);
    }
}
