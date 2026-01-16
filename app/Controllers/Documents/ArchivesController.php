<?php

declare(strict_types=1);

namespace App\Controllers\Documents;

use App\Services\Archive\ServiceArchivage;
use App\Services\Archive\ServiceIntegrite;
use App\Models\Archive;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur des archives
 * 
 * Gestion de l'archivage et vérification d'intégrité.
 * 
 * @see PRD 06 - Documents & Archives
 */
class ArchivesController
{
    /**
     * Liste les archives
     */
    public function list(): Response
    {
        $verrouille = Request::get('verrouille');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT a.*, d.type_document, d.nom_fichier
                FROM archives a
                LEFT JOIN documents_generes d ON d.id_document = a.document_id
                WHERE 1=1";
        $params = [];

        if ($verrouille !== null) {
            $sql .= " AND a.verrouille = :verrouille";
            $params['verrouille'] = $verrouille === '1' ? 1 : 0;
        }

        $sql .= " ORDER BY a.created_at DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        $archives = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $archives,
        ]);
    }

    /**
     * Affiche une archive
     */
    public function show(int $id): Response
    {
        $archive = Archive::find($id);
        if ($archive === null) {
            return Response::json(['error' => 'Archive non trouvée'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $archive->toArray(),
        ]);
    }

    /**
     * Vérifie l'intégrité d'une archive
     */
    public function verifierIntegrite(int $id): Response
    {
        try {
            $integre = ServiceArchivage::verifierIntegrite($id);

            return Response::json([
                'success' => true,
                'data' => ['integre' => $integre],
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Vérifie l'intégrité de toutes les archives
     */
    public function verifierTout(): Response
    {
        $resultats = ServiceArchivage::verifierToutesArchives();

        return Response::json([
            'success' => true,
            'data' => $resultats,
        ]);
    }

    /**
     * Verrouille une archive
     */
    public function verrouiller(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            ServiceArchivage::verrouiller($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Archive verrouillée',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne les statistiques d'archivage
     */
    public function statistiques(): Response
    {
        $stats = ServiceArchivage::getStatistiques();

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
