<?php

declare(strict_types=1);

namespace App\Controllers;

use Src\Http\Response;

/**
 * Contrôleur de la page d'accueil publique
 * 
 * Gère l'affichage de la page d'accueil pour les visiteurs non connectés.
 */
class AccueilController
{
    /**
     * GET / - Affiche la page d'accueil
     */
    public function index(): Response
    {
        // Inclure la vue d'accueil
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/accueil.php';
        $content = ob_get_clean();

        return Response::html($content);
    }
}
