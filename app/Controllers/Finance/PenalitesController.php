<?php

declare(strict_types=1);

namespace App\Controllers\Finance;

use App\Services\Finance\ServicePenalite;
use App\Services\Security\ServiceAudit;
use Src\Http\Response;
use Src\Http\Request;
use Src\Exceptions\ValidationException;

/**
 * Contrôleur des pénalités
 * 
 * Gestion des pénalités de retard.
 */
class PenalitesController
{
    private ServicePenalite $servicePenalite;

    public function __construct()
    {
        $this->servicePenalite = new ServicePenalite();
    }

    /**
     * Affiche la liste des pénalités
     */
    public function index(): Response
    {
        return Response::view('modules/finance/penalites/index');
    }

    /**
     * Liste les pénalités (API)
     */
    public function list(): Response
    {
        $etudiantId = Request::get('etudiant_id');
        $anneeId = Request::get('annee_id');
        $statut = Request::get('statut');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT p.*, e.nom_etu, e.prenom_etu, e.numero_carte
                FROM penalites p
                INNER JOIN etudiants e ON e.id_etudiant = p.etudiant_id
                WHERE 1=1";

        $params = [];

        if ($etudiantId) {
            $sql .= " AND p.etudiant_id = :etudiant";
            $params['etudiant'] = $etudiantId;
        }

        if ($anneeId) {
            $sql .= " AND p.annee_acad_id = :annee";
            $params['annee'] = $anneeId;
        }

        if ($statut === 'payee') {
            $sql .= " AND p.payee = 1";
        } elseif ($statut === 'impayee') {
            $sql .= " AND p.payee = 0 AND (p.annulee = 0 OR p.annulee IS NULL)";
        }

        $sql .= " ORDER BY p.date_application DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = \App\Orm\Model::raw($sql, $params);
        $penalites = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $penalites,
        ]);
    }

    /**
     * Affiche une pénalité
     */
    public function show(int $id): Response
    {
        $penalite = \App\Models\Penalite::find($id);
        if ($penalite === null) {
            return Response::json(['error' => 'Pénalité non trouvée'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $penalite->toArray(),
        ]);
    }

    /**
     * Crée une nouvelle pénalité
     */
    public function store(): Response
    {
        $data = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $penalite = $this->servicePenalite->creer($data, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Pénalité créée avec succès',
                'data' => [
                    'id' => $penalite->getId(),
                ],
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Enregistre le paiement d'une pénalité
     */
    public function payer(int $id): Response
    {
        $montant = (float) Request::post('montant');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $penalite = $this->servicePenalite->payerPenalite($id, $montant, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Pénalité payée',
                'data' => $penalite->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Annule une pénalité
     */
    public function annuler(int $id): Response
    {
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($motif)) {
            return Response::json(['error' => 'Le motif est requis'], 422);
        }

        try {
            $this->servicePenalite->annuler($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Pénalité annulée',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Lance le calcul automatique des pénalités
     */
    public function calculerAuto(): Response
    {
        $anneeId = Request::post('annee_id');
        if (!$anneeId) {
            $anneeId = \App\Models\AnneeAcademique::enCours()?->getId();
        }

        if (!$anneeId) {
            return Response::json(['error' => 'Année académique non trouvée'], 404);
        }

        $resultats = $this->servicePenalite->calculerToutesPenalites((int) $anneeId);

        return Response::json([
            'success' => true,
            'message' => 'Calcul des pénalités terminé',
            'data' => $resultats,
        ]);
    }

    /**
     * Retourne les pénalités d'un étudiant
     */
    public function parEtudiant(int $etudiantId): Response
    {
        $anneeId = Request::get('annee_id');

        $penalites = $this->servicePenalite->getPenalites(
            $etudiantId,
            $anneeId ? (int) $anneeId : null
        );

        return Response::json([
            'success' => true,
            'data' => $penalites,
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

        $stats = $this->servicePenalite->getStatistiques((int) $anneeId);

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
