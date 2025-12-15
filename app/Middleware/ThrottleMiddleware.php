<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware de limitation de débit (Throttle)
 */
class ThrottleMiddleware
{
    private int $maxAttempts = 60;
    private int $decayMinutes = 1;

    public function handle(callable $next): void
    {
        // Simplification: pas de vraie implémentation Redis/Cache ici pour l'instant
        // Juste un placeholder pour l'architecture
        $next();
    }
}
