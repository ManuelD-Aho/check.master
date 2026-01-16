<?php

declare(strict_types=1);

namespace App\Controllers\Documents;

use App\Services\Documents\ServicePdf;
use App\Models\DocumentGenere;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur des documents
 * 
 * Génération et gestion des documents PDF.
 * 
 * @see PRD 06 - Documents & Archives
 */
class DocumentsController
{
    /**
     * Liste les documents générés
     */
    public function list(): Response
    {
        $type = Request::get('type');
        $entiteType = Request::get('entite_type');
        $entiteId = Request::get('entite_id');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT * FROM documents_generes WHERE 1=1";
        $params = [];

        if ($type) {
            $sql .= " AND type_document = :type";
            $params['type'] = $type;
        }

        if ($entiteType) {
            $sql .= " AND entite_type = :entite_type";
            $params['entite_type'] = $entiteType;
        }

        if ($entiteId) {
            $sql .= " AND entite_id = :entite_id";
            $params['entite_id'] = $entiteId;
        }

        $sql .= " ORDER BY created_at DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Affiche un document
     */
    public function show(int $id): Response
    {
        $document = DocumentGenere::find($id);
        if ($document === null) {
            return Response::json(['error' => 'Document non trouvé'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $document->toArray(),
        ]);
    }

    /**
     * Télécharge un document
     */
    public function telecharger(int $id): Response
    {
        $document = DocumentGenere::find($id);
        if ($document === null || empty($document->chemin_fichier)) {
            return Response::json(['error' => 'Document non trouvé'], 404);
        }

        if (!file_exists($document->chemin_fichier)) {
            return Response::json(['error' => 'Fichier non trouvé'], 404);
        }

        return Response::download($document->chemin_fichier, $document->nom_fichier);
    }

    /**
     * Génère un document
     */
    public function generer(): Response
    {
        $type = Request::post('type');
        $donnees = Request::post('donnees');
        $entiteType = Request::post('entite_type');
        $entiteId = Request::post('entite_id');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($type) || empty($donnees)) {
            return Response::json(['error' => 'Le type et les données sont requis'], 422);
        }

        if (is_string($donnees)) {
            $donnees = json_decode($donnees, true) ?? [];
        }

        try {
            $resultat = ServicePdf::generer(
                $type,
                $donnees,
                (int) $utilisateurId,
                $entiteType,
                $entiteId ? (int) $entiteId : null
            );

            return Response::json([
                'success' => true,
                'message' => 'Document généré avec succès',
                'data' => $resultat,
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Régénère un document
     */
    public function regenerer(int $id): Response
    {
        $document = DocumentGenere::find($id);
        if ($document === null) {
            return Response::json(['error' => 'Document non trouvé'], 404);
        }

        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        // Récupérer les données originales si disponibles
        $donnees = json_decode($document->donnees_json ?? '{}', true);

        try {
            $resultat = ServicePdf::generer(
                $document->type_document,
                $donnees,
                (int) $utilisateurId,
                $document->entite_type,
                $document->entite_id ? (int) $document->entite_id : null
            );

            return Response::json([
                'success' => true,
                'message' => 'Document régénéré avec succès',
                'data' => $resultat,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne les types de documents disponibles
     */
    public function types(): Response
    {
        $types = [
            ServicePdf::TYPE_RECU_PAIEMENT => 'Reçu de paiement',
            ServicePdf::TYPE_RECU_PENALITE => 'Reçu de pénalité',
            ServicePdf::TYPE_BULLETIN_NOTES => 'Bulletin de notes',
            ServicePdf::TYPE_PV_COMMISSION => 'PV de commission',
            ServicePdf::TYPE_PV_SOUTENANCE => 'PV de soutenance',
            ServicePdf::TYPE_CONVOCATION => 'Convocation',
            ServicePdf::TYPE_ATTESTATION_DIPLOME => 'Attestation de diplôme',
            ServicePdf::TYPE_RAPPORT_EVALUATION => 'Rapport d\'évaluation',
            ServicePdf::TYPE_BULLETIN_PROVISOIRE => 'Bulletin provisoire',
            ServicePdf::TYPE_CERTIFICAT_SCOLARITE => 'Certificat de scolarité',
            ServicePdf::TYPE_LETTRE_JURY => 'Lettre jury',
            ServicePdf::TYPE_ATTESTATION_STAGE => 'Attestation de stage',
            ServicePdf::TYPE_BORDEREAU_TRANSMISSION => 'Bordereau de transmission',
        ];

        return Response::json([
            'success' => true,
            'data' => $types,
        ]);
    }
}
