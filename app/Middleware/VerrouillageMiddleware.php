<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Utilisateur;

/**
 * Middleware pour verrouiller les sessions inactives
 */
class VerrouillageMiddleware
{
    public function handle(callable $next): void
    {
        if (isset($_SESSION['user_id'])) {
            $user = Utilisateur::find($_SESSION['user_id']);
            if ($user && $user->estVerrouille()) {
                // Rediriger vers page de déverrouillage
                header('Location: /locked');
                exit;
            }
        }

        $next();
    }
}
