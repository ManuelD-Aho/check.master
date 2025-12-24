<?php

declare(strict_types=1);

namespace App\Controllers;

use Src\Http\Response;
use Src\Support\Auth;

/**
 * Contrôleur du tableau de bord
 * 
 * Gère l'affichage du dashboard principal pour les utilisateurs connectés.
 */
class DashboardController
{
    /**
     * GET /dashboard - Affiche le tableau de bord
     */
    public function index(): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return Response::redirect('/connexion');
        }

        // Inclure la vue du dashboard admin
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/admin/dashboard.php';
        $content = ob_get_clean();

        return Response::html($content);
    }
}
