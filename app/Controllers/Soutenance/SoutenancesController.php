<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Soutenance\ServiceSoutenance;
use App\Services\Soutenance\ServiceCalendrier;
use Src\Http\Response;
use Src\Http\Request;
use Src\Exceptions\ValidationException;

/**
 * Contrôleur des soutenances
 * 
 * Gestion des soutenances: planification, déroulement, finalisation.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class SoutenancesController
{
    private ServiceSoutenance $serviceSoutenance;
    private ServiceCalendrier $serviceCalendrier;

    public function __construct()
    {
        $this->serviceSoutenance = new ServiceSoutenance();
        $this->serviceCalendrier = new ServiceCalendrier();
    }

    /**
     * Affiche la page principale des soutenances
     */
    public function index(): Response
    {
        return Response::view('modules/soutenance/index');
    }

    /**
     * Liste les soutenances (API)
     */
    public function list(): Response
    {
        $statut = Request::get('statut');
        $dateDebut = Request::get('date_debut');
        $dateFin = Request::get('date_fin');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT s.*, sa.nom_salle, e.nom_etu, e.prenom_etu, c.theme
                FROM soutenances s
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN candidatures c ON c.dossier_id = de.id_dossier
                LEFT JOIN salles sa ON sa.id_salle = s.salle_id
                WHERE 1=1";

        $params = [];

        if ($statut) {
            $sql .= " AND s.statut = :statut";
            $params['statut'] = $statut;
        }

        if ($dateDebut) {
            $sql .= " AND s.date_soutenance >= :date_debut";
            $params['date_debut'] = $dateDebut;
        }

        if ($dateFin) {
            $sql .= " AND s.date_soutenance <= :date_fin";
            $params['date_fin'] = $dateFin;
        }

        $sql .= " ORDER BY s.date_soutenance DESC, s.heure_debut";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = \App\Orm\Model::raw($sql, $params);
        $soutenances = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $soutenances,
        ]);
    }

    /**
     * Affiche une soutenance
     */
    public function show(int $id): Response
    {
        try {
            $details = $this->serviceSoutenance->getDetails($id);

            return Response::json([
                'success' => true,
                'data' => $details,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Planifie une nouvelle soutenance
     */
    public function store(): Response
    {
        $dossierId = (int) Request::post('dossier_id');
        $dateSoutenance = Request::post('date_soutenance');
        $heureDebut = Request::post('heure_debut');
        $heureFin = Request::post('heure_fin');
        $salleId = (int) Request::post('salle_id');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $soutenance = $this->serviceCalendrier->planifier(
                $dossierId,
                $dateSoutenance,
                $heureDebut,
                $heureFin,
                $salleId,
                (int) $utilisateurId
            );

            return Response::json([
                'success' => true,
                'message' => 'Soutenance planifiée avec succès',
                'data' => ['id' => $soutenance->getId()],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Démarre une soutenance
     */
    public function demarrer(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $soutenance = $this->serviceSoutenance->demarrer($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Soutenance démarrée',
                'data' => $soutenance->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Termine une soutenance
     */
    public function terminer(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $resultats = $this->serviceSoutenance->terminer($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Soutenance terminée',
                'data' => $resultats,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Reporte une soutenance
     */
    public function reporter(int $id): Response
    {
        $nouvelleDate = Request::post('nouvelle_date');
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($nouvelleDate) || empty($motif)) {
            return Response::json(['error' => 'La nouvelle date et le motif sont requis'], 422);
        }

        try {
            $soutenance = $this->serviceSoutenance->reporter($id, $nouvelleDate, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Soutenance reportée',
                'data' => $soutenance->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Demande des corrections
     */
    public function demanderCorrections(int $id): Response
    {
        $corrections = Request::post('corrections');
        $delai = (int) (Request::post('delai_jours') ?? 30);
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($corrections)) {
            return Response::json(['error' => 'La liste des corrections est requise'], 422);
        }

        try {
            $this->serviceSoutenance->demanderCorrections($id, $corrections, $delai, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Corrections demandées',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Valide les corrections
     */
    public function validerCorrections(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $this->serviceSoutenance->validerCorrections($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Corrections validées',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Télécharge le PV de soutenance
     */
    public function telechargerPV(int $id): Response
    {
        $soutenance = \App\Models\Soutenance::find($id);
        if ($soutenance === null || empty($soutenance->chemin_pv)) {
            return Response::json(['error' => 'PV non trouvé'], 404);
        }

        if (!file_exists($soutenance->chemin_pv)) {
            return Response::json(['error' => 'Fichier non trouvé'], 404);
        }

        return Response::download($soutenance->chemin_pv);
    }

    /**
     * Retourne le planning du jour
     */
    public function planningJour(): Response
    {
        $date = Request::get('date') ?? date('Y-m-d');
        $planning = $this->serviceCalendrier->getPlanningJour($date);

        return Response::json([
            'success' => true,
            'data' => $planning,
        ]);
    }

    /**
     * Retourne les soutenances à venir
     */
    public function aVenir(): Response
    {
        $jours = (int) (Request::get('jours') ?? 30);
        $soutenances = $this->serviceSoutenance->getSoutenancesAVenir($jours);

        return Response::json([
            'success' => true,
            'data' => $soutenances,
        ]);
    }

    /**
     * Retourne les statistiques
     */
    public function statistiques(): Response
    {
        $stats = $this->serviceSoutenance->getStatistiques();

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
