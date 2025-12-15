<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware pour forcer/gérer les réponses JSON
 */
class JsonMiddleware
{
    public function handle(callable $next): void
    {
        // Enforce JSON header if needed, or decode input
        header('Content-Type: application/json; charset=utf-8');

        // Decode JSON body if present
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $data = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $_POST = array_merge($_POST, $data);
            }
        }

        $next();
    }
}
