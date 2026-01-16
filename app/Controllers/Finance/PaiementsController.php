<?php

declare(strict_types=1);

namespace App\Controllers\Finance;

use App\Services\Finance\ServicePaiement;
use App\Services\Finance\ServicePenalite;
use App\Services\Finance\ServiceExoneration;
use App\Services\Security\ServiceAudit;
use Src\Http\Response;
use Src\Http\Request;
use Src\Exceptions\ValidationException;

/**
 * Contrôleur des paiements
 * 
 * Gestion des paiements de scolarité.
 */
class PaiementsController
{
    private ServicePaiement $servicePaiement;

    public function __construct()
    {
        $this->servicePaiement = new ServicePaiement();
    }

    /**
     * Affiche la liste des paiements
     */
    public function index(): Response
    {
        return Response::view('modules/finance/paiements/index');
    }

    /**
     * Liste les paiements (API)
     */
    public function list(): Response
    {
        $etudiantId = Request::get('etudiant_id');
        $anneeId = Request::get('annee_id');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT p.*, e.nom_etu, e.prenom_etu, e.numero_carte,
                       aa.libelle_annee_acad
                FROM paiements p
                INNER JOIN etudiants e ON e.id_etudiant = p.etudiant_id
                LEFT JOIN annees_academiques aa ON aa.id_annee_acad = p.annee_acad_id
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

        $sql .= " ORDER BY p.date_paiement DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = \App\Orm\Model::raw($sql, $params);
        $paiements = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $paiements,
        ]);
    }

    /**
     * Affiche un paiement
     */
    public function show(int $id): Response
    {
        $paiement = \App\Models\Paiement::find($id);
        if ($paiement === null) {
            return Response::json(['error' => 'Paiement non trouvé'], 404);
        }

        return Response::json([
            'success' => true,
            'data' => $paiement->toArray(),
        ]);
    }

    /**
     * Enregistre un nouveau paiement
     */
    public function store(): Response
    {
        $data = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $paiement = $this->servicePaiement->enregistrer($data, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'data' => [
                    'id' => $paiement->getId(),
                    'reference' => $paiement->reference,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Annule un paiement
     */
    public function annuler(int $id): Response
    {
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($motif)) {
            return Response::json(['error' => 'Le motif est requis'], 422);
        }

        try {
            $this->servicePaiement->annuler($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Paiement annulé',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne le solde d'un étudiant
     */
    public function solde(int $etudiantId): Response
    {
        $anneeId = Request::get('annee_id');
        if (!$anneeId) {
            $anneeId = \App\Models\AnneeAcademique::enCours()?->getId();
        }

        if (!$anneeId) {
            return Response::json(['error' => 'Année académique non trouvée'], 404);
        }

        $solde = $this->servicePaiement->calculerSolde($etudiantId, (int) $anneeId);

        return Response::json([
            'success' => true,
            'data' => $solde,
        ]);
    }

    /**
     * Retourne l'historique des paiements d'un étudiant
     */
    public function historique(int $etudiantId): Response
    {
        $anneeId = Request::get('annee_id');

        $historique = $this->servicePaiement->getHistorique(
            $etudiantId,
            $anneeId ? (int) $anneeId : null
        );

        return Response::json([
            'success' => true,
            'data' => $historique,
        ]);
    }

    /**
     * Télécharge le reçu d'un paiement
     */
    public function telechargerRecu(int $id): Response
    {
        $chemin = $this->servicePaiement->getRecu($id);

        if ($chemin === null || !file_exists($chemin)) {
            return Response::json(['error' => 'Reçu non trouvé'], 404);
        }

        return Response::download($chemin);
    }

    /**
     * Régénère un reçu
     */
    public function regenererRecu(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $resultat = $this->servicePaiement->regenererRecu($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Reçu régénéré',
                'data' => $resultat,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
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

        $stats = $this->servicePaiement->getStatistiques((int) $anneeId);

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
