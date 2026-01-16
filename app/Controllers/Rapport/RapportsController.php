<?php

declare(strict_types=1);

namespace App\Controllers\Rapport;

use App\Services\Rapport\ServiceRapport;
use App\Models\RapportEtudiant;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur des rapports étudiants
 * 
 * Gestion des rapports de stage/mémoire.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class RapportsController
{
    private ServiceRapport $serviceRapport;

    public function __construct()
    {
        $this->serviceRapport = new ServiceRapport();
    }

    /**
     * Liste les rapports
     */
    public function list(): Response
    {
        $dossierId = Request::get('dossier_id');
        $statut = Request::get('statut');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT r.*, e.nom_etu, e.prenom_etu, de.id_dossier
                FROM rapports_etudiants r
                INNER JOIN dossiers_etudiants de ON de.id_dossier = r.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE 1=1";

        $params = [];

        if ($dossierId) {
            $sql .= " AND r.dossier_id = :dossier";
            $params['dossier'] = $dossierId;
        }

        if ($statut) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $statut;
        }

        $sql .= " ORDER BY r.created_at DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        $rapports = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $rapports,
        ]);
    }

    /**
     * Affiche un rapport
     */
    public function show(int $id): Response
    {
        $rapport = RapportEtudiant::find($id);
        if ($rapport === null) {
            return Response::json(['error' => 'Rapport non trouvé'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $rapport->toArray(),
        ]);
    }

    /**
     * Crée un nouveau rapport
     */
    public function store(): Response
    {
        $dossierId = (int) Request::post('dossier_id');
        $donnees = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $rapport = $this->serviceRapport->creer($dossierId, $donnees, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Rapport créé avec succès',
                'data' => ['id' => $rapport->getId()],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Met à jour un rapport
     */
    public function update(int $id): Response
    {
        $rapport = RapportEtudiant::find($id);
        if ($rapport === null) {
            return Response::json(['error' => 'Rapport non trouvé'], 404);
        }

        $titre = Request::post('titre');
        if ($titre) {
            $rapport->titre = $titre;
        }

        $rapport->save();

        return Response::json([
            'success' => true,
            'message' => 'Rapport mis à jour',
            'data' => $rapport->toArray(),
        ]);
    }

    /**
     * Soumet un rapport
     */
    public function soumettre(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $this->serviceRapport->soumettre($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Rapport soumis avec succès',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Upload un fichier de rapport
     */
    public function uploadFichier(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($_FILES['fichier'])) {
            return Response::json(['error' => 'Fichier requis'], 422);
        }

        try {
            $resultat = $this->serviceRapport->attacherFichier(
                $id,
                $_FILES['fichier'],
                (int) $utilisateurId
            );

            return Response::json([
                'success' => true,
                'message' => 'Fichier uploadé',
                'data' => $resultat,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne l'historique des versions
     */
    public function versions(int $id): Response
    {
        $rapport = RapportEtudiant::find($id);
        if ($rapport === null) {
            return Response::json(['error' => 'Rapport non trouvé'], 404);
        }

        $versions = $this->serviceRapport->getHistoriqueVersions((int) $rapport->dossier_id);

        return Response::json([
            'success' => true,
            'data' => $versions,
        ]);
    }

    /**
     * Crée une nouvelle version du rapport
     */
    public function creerVersion(int $id): Response
    {
        $donnees = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $nouvelleVersion = $this->serviceRapport->creerVersion($id, $donnees, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Nouvelle version créée',
                'data' => [
                    'id' => $nouvelleVersion->getId(),
                    'version' => $nouvelleVersion->version,
                ],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }
}
