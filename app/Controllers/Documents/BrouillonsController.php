<?php

declare(strict_types=1);

namespace App\Controllers\Documents;

use App\Services\Archive\ServiceBrouillon;
use Src\Http\Response;
use Src\Http\Request;

/**
 * Contrôleur des brouillons
 * 
 * Sauvegarde automatique des formulaires.
 * 
 * @see PRD 06 - Documents & Archives
 */
class BrouillonsController
{
    /**
     * Liste les brouillons de l'utilisateur
     */
    public function list(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $brouillons = ServiceBrouillon::listerBrouillons((int) $utilisateurId);

        return Response::json([
            'success' => true,
            'data' => $brouillons,
        ]);
    }

    /**
     * Récupère un brouillon
     */
    public function recuperer(string $type, int $ctx, string $code): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $brouillon = ServiceBrouillon::recuperer(
            (int) $utilisateurId,
            $type,
            $ctx > 0 ? $ctx : null,
            $code
        );

        if ($brouillon === null) {
            return Response::json([
                'success' => true,
                'data' => null,
            ]);
        }

        return Response::json([
            'success' => true,
            'data' => $brouillon,
        ]);
    }

    /**
     * Sauvegarde un brouillon
     */
    public function sauvegarder(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $typeContexte = Request::post('type_contexte');
        $contexteId = Request::post('contexte_id');
        $codeFormulaire = Request::post('code_formulaire');
        $donnees = Request::post('donnees');

        if (empty($typeContexte) || empty($codeFormulaire) || empty($donnees)) {
            return Response::json(['error' => 'Type, code et données requis'], 422);
        }

        if (is_string($donnees)) {
            $donnees = json_decode($donnees, true) ?? [];
        }

        $resultat = ServiceBrouillon::sauvegarder(
            (int) $utilisateurId,
            $typeContexte,
            $contexteId ? (int) $contexteId : null,
            $codeFormulaire,
            $donnees
        );

        return Response::json([
            'success' => true,
            'message' => 'Brouillon sauvegardé',
            'data' => $resultat,
        ]);
    }

    /**
     * Supprime un brouillon
     */
    public function supprimer(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $supprime = ServiceBrouillon::supprimer($id, (int) $utilisateurId);

        if (!$supprime) {
            return Response::json(['error' => 'Brouillon non trouvé'], 404);
        }

        return Response::json([
            'success' => true,
            'message' => 'Brouillon supprimé',
        ]);
    }
}
