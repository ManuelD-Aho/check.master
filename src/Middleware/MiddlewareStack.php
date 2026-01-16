<?php

declare(strict_types=1);

namespace Src\Middleware;

use Src\Http\Request;
use Src\Http\Response;

/**
 * Middleware Stack - Gestionnaire de middlewares
 * 
 * @package Src\Middleware
 */
class MiddlewareStack
{
    private array $middlewares = [];

    /**
     * Ajouter un middleware
     *
     * @param callable $middleware Middleware
     * @return self
     */
    public function add(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Exécuter la stack
     *
     * @param Request $request Requête
     * @param callable $core Action core
     * @return Response Réponse
     */
    public function handle(Request $request, callable $core): Response
    {
        $stack = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $middleware) => fn($req) => $middleware($req, $next),
            $core
        );

        return $stack($request);
    }
}
