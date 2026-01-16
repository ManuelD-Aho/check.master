<?php

declare(strict_types=1);

namespace App\Controllers\Documents;

use App\Services\Archive\ServiceHistorique;
use Src\Http\Response;
use Src\Http\Request;

/**
 * Contrôleur de l'historique des entités
 * 
 * Versioning et comparaison d'entités.
 * 
 * @see PRD 06 - Documents & Archives
 */
class HistoriqueController
{
    /**
     * Affiche l'historique d'une entité
     */
    public function show(string $type, int $id): Response
    {
        $historique = ServiceHistorique::getHistorique($type, $id);

        return Response::json([
            'success' => true,
            'data' => [
                'type_entite' => $type,
                'entite_id' => $id,
                'versions' => $historique,
                'nombre_versions' => count($historique),
            ],
        ]);
    }

    /**
     * Affiche une version spécifique
     */
    public function version(string $type, int $id, int $version): Response
    {
        $versionData = ServiceHistorique::getVersion($type, $id, $version);

        if ($versionData === null) {
            return Response::json(['error' => 'Version non trouvée'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $versionData,
        ]);
    }

    /**
     * Compare deux versions
     */
    public function comparer(string $type, int $id): Response
    {
        $version1 = (int) Request::get('v1');
        $version2 = (int) Request::get('v2');

        if ($version1 <= 0 || $version2 <= 0) {
            return Response::json(['error' => 'Les versions v1 et v2 sont requises'], 422);
        }

        $diff = ServiceHistorique::comparer($type, $id, $version1, $version2);

        if (isset($diff['erreur'])) {
            return Response::json(['error' => $diff['erreur']], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $diff,
        ]);
    }

    /**
     * Restaure une version
     */
    public function restaurer(string $type, int $id, int $version): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        $success = ServiceHistorique::restaurer($type, $id, $version, (int) $utilisateurId);

        if (!$success) {
            return Response::json(['error' => 'Impossible de restaurer cette version'], 400);
        }

        return Response::json([
            'success' => true,
            'message' => 'Version restaurée avec succès',
        ]);
    }
}
