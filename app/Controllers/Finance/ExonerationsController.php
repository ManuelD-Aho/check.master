<?php

declare(strict_types=1);

namespace App\Controllers\Finance;

use App\Services\Finance\ServiceExoneration;
use App\Services\Security\ServiceAudit;
use Src\Http\Response;
use Src\Http\Request;
use Src\Exceptions\ValidationException;

/**
 * Contrôleur des exonérations
 * 
 * Gestion des demandes d'exonération.
 */
class ExonerationsController
{
    private ServiceExoneration $serviceExoneration;

    public function __construct()
    {
        $this->serviceExoneration = new ServiceExoneration();
    }

    /**
     * Affiche la liste des exonérations
     */
    public function index(): Response
    {
        return Response::view('modules/finance/exonerations/index');
    }

    /**
     * Liste les exonérations (API)
     */
    public function list(): Response
    {
        $etudiantId = Request::get('etudiant_id');
        $anneeId = Request::get('annee_id');
        $statut = Request::get('statut');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT ex.*, e.nom_etu, e.prenom_etu, e.numero_carte,
                       aa.libelle_annee_acad
                FROM exonerations ex
                INNER JOIN etudiants e ON e.id_etudiant = ex.etudiant_id
                LEFT JOIN annees_academiques aa ON aa.id_annee_acad = ex.annee_acad_id
                WHERE 1=1";

        $params = [];

        if ($etudiantId) {
            $sql .= " AND ex.etudiant_id = :etudiant";
            $params['etudiant'] = $etudiantId;
        }

        if ($anneeId) {
            $sql .= " AND ex.annee_acad_id = :annee";
            $params['annee'] = $anneeId;
        }

        if ($statut) {
            $sql .= " AND ex.statut = :statut";
            $params['statut'] = $statut;
        }

        $sql .= " ORDER BY ex.demandee_le DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = \App\Orm\Model::raw($sql, $params);
        $exonerations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $exonerations,
        ]);
    }

    /**
     * Affiche une exonération
     */
    public function show(int $id): Response
    {
        $exoneration = \App\Models\Exoneration::find($id);
        if ($exoneration === null) {
            return Response::json(['error' => 'Exonération non trouvée'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $exoneration->toArray(),
        ]);
    }

    /**
     * Crée une demande d'exonération
     */
    public function store(): Response
    {
        $data = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $exoneration = $this->serviceExoneration->creer($data, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Demande d\'exonération créée avec succès',
                'data' => [
                    'id' => $exoneration->getId(),
                ],
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Approuve une exonération
     */
    public function approuver(int $id): Response
    {
        $commentaire = Request::post('commentaire');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $exoneration = $this->serviceExoneration->approuver(
                $id,
                (int) $utilisateurId,
                $commentaire
            );

            return Response::json([
                'success' => true,
                'message' => 'Exonération approuvée',
                'data' => $exoneration->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Refuse une exonération
     */
    public function refuser(int $id): Response
    {
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($motif)) {
            return Response::json(['error' => 'Le motif de refus est requis'], 422);
        }

        try {
            $exoneration = $this->serviceExoneration->refuser($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Exonération refusée',
                'data' => $exoneration->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Annule une exonération
     */
    public function annuler(int $id): Response
    {
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($motif)) {
            return Response::json(['error' => 'Le motif est requis'], 422);
        }

        try {
            $this->serviceExoneration->annuler($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Exonération annulée',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne les exonérations en attente
     */
    public function enAttente(): Response
    {
        $exonerations = $this->serviceExoneration->getEnAttente();

        return Response::json([
            'success' => true,
            'data' => $exonerations,
        ]);
    }

    /**
     * Retourne les exonérations d'un étudiant
     */
    public function parEtudiant(int $etudiantId): Response
    {
        $anneeId = Request::get('annee_id');

        $exonerations = $this->serviceExoneration->getExonerations(
            $etudiantId,
            $anneeId ? (int) $anneeId : null
        );

        return Response::json([
            'success' => true,
            'data' => $exonerations,
        ]);
    }

    /**
     * Retourne les statistiques
     */
    public function statistiques(): Response
    {
        $anneeId = Request::get('annee_id');
        if (!$anneeId) {
            $anneeId = \App\Models\AnneeAcademique::enCours()?->getId();
        }

        if (!$anneeId) {
            return Response::json(['error' => 'Année académique non trouvée'], 404);
        }

        $stats = $this->serviceExoneration->getStatistiques((int) $anneeId);

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
