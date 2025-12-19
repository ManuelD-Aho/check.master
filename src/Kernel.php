<?php

declare(strict_types=1);

namespace Src;

use Src\Http\Request;
use Src\Http\Response;
use Src\Exceptions\AppException;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\JsonMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Middleware\SecurityHeadersMiddleware;

/**
 * Noyau de l'application
 * 
 * Gère le cycle de vie de la requête HTTP
 */
class Kernel
{
    /**
     * Middleware global (exécuté à chaque requête)
     */
    protected array $middleware = [
        SecurityHeadersMiddleware::class,
        LoggingMiddleware::class,
        CsrfMiddleware::class,
    ];

    /**
     * Middleware de route (exécuté selon la logique)
     */
    protected array $routeMiddleware = [
        'auth' => AuthMiddleware::class,
        'json' => JsonMiddleware::class,
    ];

    /**
     * Gère la requête entrante
     */
    public function handle(Request $request): Response
    {
        try {
            // 1. Exécuter le middleware global
            return $this->runMiddleware($request, function ($req) {
                // 2. Router la requête (simplifié pour l'exemple, normalement via Router)
                return $this->dispatch($req);
            });
        } catch (\Throwable $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Dispatch la requête vers le bon contrôleur
     */
    protected function dispatch(Request $request): Response
    {
        // Logique de routing basique ou appel au Router
        // Pour l'instant on retourne une 404 si pas géré ailleurs
        // Dans une vraie app, on utiliserait un Router dédié

        // Exemple simple : si on est là, c'est que le router global n'a pas matché avant
        // Ou alors c'est ici qu'on appellerait le Router

        // Simuler une réponse 200 pour la home
        if ($request::uri() === '/') {
            // return Response::html('<h1>CheckMaster Home</h1>');
        }

        return Response::text('Not Found', 404);
    }

    /**
     * Exécute la pile de middleware
     */
    protected function runMiddleware(Request $request, callable $destination): Response
    {
        $pipeline = array_reverse($this->middleware);

        $next = $destination;

        foreach ($pipeline as $middlewareClass) {
            $next = function () use ($middlewareClass, $next) {
                $middleware = new $middlewareClass();
                // Si le middleware attend $request en paramètre ou utilise le singleton
                // Ici on suppose qu'il utilise le singleton ou qu'on adapte
                return $middleware->handle($next);
            };
        }

        return $next($request); // On passe $request par acquis de conscience
    }

    /**
     * Gère les exceptions non attrapées
     */
    protected function handleException(\Throwable $e, Request $request): Response
    {
        // Logger l'erreur
        if (function_exists('logger')) {
            logger()->error($e->getMessage(), ['exception' => $e]);
        }

        $statusCode = 500;
        if ($e instanceof AppException) {
            $statusCode = $e->getHttpCode();
        }

        if ($request::isAjax() || $request::header('Accept') === 'application/json') {
            return Response::text(json_encode([
                'error' => true,
                'message' => APP_DEBUG ? $e->getMessage() : 'Une erreur est survenue',
            ]), $statusCode)->header('Content-Type', 'application/json');
        }

        $content = APP_DEBUG
            ? "<h1>Erreur {$statusCode}</h1><pre>{$e}</pre>"
            : "<h1>Une erreur est survenue</h1><p>Veuillez réessayer plus tard.</p>";

        return Response::html($content, $statusCode);
    }
}
