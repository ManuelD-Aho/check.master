<?php

declare(strict_types=1);

namespace App\Controllers\Workflow;

use App\Services\Workflow\ServiceWorkflow;
use App\Services\Workflow\ServiceEscalade;
use App\Services\Security\ServicePermissions;
use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;
use App\Models\WorkflowTransition;
use App\Models\WorkflowHistorique;
use App\Models\Escalade;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Workflow
 * 
 * Gère les opérations sur le workflow de suivi des dossiers:
 * - Transitions d'état
 * - Visualisation du workflow
 * - Gestion des escalades
 * - Alertes SLA
 * 
 * @see PRD 03 - Workflow & Commission
 */
class WorkflowController
{
    private ServiceWorkflow $serviceWorkflow;
    private ServiceEscalade $serviceEscalade;

    public function __construct()
    {
        $this->serviceWorkflow = new ServiceWorkflow();
        $this->serviceEscalade = new ServiceEscalade();
    }

    // =========================================================================
    // WORKFLOW - ÉTATS & TRANSITIONS
    // =========================================================================

    /**
     * Vue de gestion du workflow
     */
    public function index(): Response
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $stats = $this->serviceWorkflow->getStatistiques();
        $etats = WorkflowEtat::all();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/workflow/index.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste tous les états du workflow
     */
    public function listEtats(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $etats = WorkflowEtat::all();
        return JsonResponse::success(array_map(fn($e) => $e->toArray(), $etats));
    }

    /**
     * API: Détails d'un état
     */
    public function showEtat(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $etat = WorkflowEtat::find($id);
        if ($etat === null) {
            return JsonResponse::notFound('État non trouvé');
        }

        $data = $etat->toArray();
        
        // Récupérer les transitions depuis et vers cet état
        $data['transitions_depuis'] = array_map(fn($t) => $t->toArray(), WorkflowTransition::depuisEtat($id));
        $data['transitions_vers'] = array_map(fn($t) => $t->toArray(), WorkflowTransition::versEtat($id));

        return JsonResponse::success($data);
    }

    /**
     * API: Liste toutes les transitions
     */
    public function listTransitions(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $transitions = WorkflowTransition::all();
        $result = [];

        foreach ($transitions as $transition) {
            $data = $transition->toArray();
            $data['etat_source'] = $transition->etatSource()?->toArray();
            $data['etat_cible'] = $transition->etatCible()?->toArray();
            $result[] = $data;
        }

        return JsonResponse::success($result);
    }

    /**
     * API: Transitions possibles pour un dossier
     */
    public function transitionsPossibles(int $dossierId): JsonResponse
    {
        if (!$this->checkAccess('dossiers', 'lire')) {
            return JsonResponse::forbidden();
        }

        $transitions = $this->serviceWorkflow->getTransitionsPossibles($dossierId);
        return JsonResponse::success($transitions);
    }

    /**
     * API: Effectue une transition sur un dossier
     */
    public function effectuerTransition(int $dossierId): JsonResponse
    {
        if (!$this->checkAccess('dossiers', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $etatCible = Request::input('etat_cible', '');
        $commentaire = Request::input('commentaire', '');

        if (empty($etatCible)) {
            return JsonResponse::error('État cible requis');
        }

        try {
            $this->serviceWorkflow->effectuerTransition(
                $dossierId,
                $etatCible,
                Auth::id() ?? 0,
                $commentaire ?: null
            );

            return JsonResponse::success(null, 'Transition effectuée avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Historique workflow d'un dossier
     */
    public function historiqueDossier(int $dossierId): JsonResponse
    {
        if (!$this->checkAccess('dossiers', 'lire')) {
            return JsonResponse::forbidden();
        }

        $historique = $this->serviceWorkflow->getHistorique($dossierId);
        
        $result = [];
        foreach ($historique as $entry) {
            $data = $entry->toArray();
            $data['etat_source'] = $entry->etatSource()?->toArray();
            $data['etat_cible'] = $entry->etatCible()?->toArray();
            $data['utilisateur'] = $entry->utilisateur()?->nom_utilisateur;
            $result[] = $data;
        }

        return JsonResponse::success($result);
    }

    /**
     * API: Statistiques du workflow
     */
    public function statistiquesWorkflow(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $stats = $this->serviceWorkflow->getStatistiques();
        return JsonResponse::success($stats);
    }

    /**
     * API: Transitions récentes (tous dossiers)
     */
    public function transitionsRecentes(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $limit = min(100, max(10, (int) Request::query('limit', 50)));
        $transitions = WorkflowHistorique::recentes($limit);

        return JsonResponse::success($transitions);
    }

    // =========================================================================
    // ALERTES SLA
    // =========================================================================

    /**
     * API: Vérifie et récupère les alertes SLA
     */
    public function alertesSLA(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $alertes = $this->serviceWorkflow->verifierSLA();
        return JsonResponse::success($alertes);
    }

    /**
     * API: Dossiers en dépassement de délai
     */
    public function dossiersEnRetard(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $sql = "SELECT de.*, we.nom_etat, we.code_etat, e.nom_etu, e.prenom_etu,
                       DATEDIFF(NOW(), de.date_limite_etat) as jours_retard
                FROM dossiers_etudiants de
                INNER JOIN workflow_etats we ON we.id_etat = de.etat_actuel_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE de.date_limite_etat < NOW()
                ORDER BY jours_retard DESC";

        $stmt = DossierEtudiant::raw($sql, []);
        $dossiers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return JsonResponse::success($dossiers);
    }

    // =========================================================================
    // ESCALADES
    // =========================================================================

    /**
     * Vue de gestion des escalades
     */
    public function indexEscalades(): Response
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $escalades = Escalade::ouvertes();
        $stats = $this->serviceEscalade->getStatistiques();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/workflow/escalades.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste des escalades
     */
    public function listEscalades(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $statut = Request::query('statut') ?: null;
        $type = Request::query('type') ?: null;

        if ($statut === 'ouvertes') {
            $escalades = Escalade::ouvertes();
        } elseif ($statut === 'en_cours') {
            $escalades = Escalade::enCours();
        } else {
            $escalades = Escalade::all();
        }

        $result = [];
        foreach ($escalades as $escalade) {
            $data = $escalade->toArray();
            $data['dossier'] = $escalade->dossier()?->toArray();
            $data['createur'] = $escalade->createur()?->nom_utilisateur;
            $data['assigne_a'] = $escalade->assigneA()?->nom_utilisateur;
            $result[] = $data;
        }

        return JsonResponse::success($result);
    }

    /**
     * API: Détails d'une escalade
     */
    public function showEscalade(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $escalade = Escalade::find($id);
        if ($escalade === null) {
            return JsonResponse::notFound('Escalade non trouvée');
        }

        $data = $escalade->toArray();
        $data['dossier'] = $escalade->dossier()?->toArray();
        $data['createur'] = $escalade->createur()?->toArray();
        $data['assigne_a'] = $escalade->assigneA()?->toArray();
        $data['actions'] = array_map(fn($a) => $a->toArray(), $escalade->actions());

        return JsonResponse::success($data);
    }

    /**
     * API: Création d'une escalade
     */
    public function storeEscalade(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'creer')) {
            return JsonResponse::forbidden();
        }

        $dossierId = (int) Request::input('dossier_id', 0);
        $type = Request::input('type', Escalade::TYPE_AUTRE);
        $description = Request::input('description', '');

        if ($dossierId === 0 || empty($description)) {
            return JsonResponse::error('Dossier et description requis');
        }

        try {
            $escalade = $this->serviceEscalade->creerEscalade(
                $dossierId,
                $type,
                $description,
                Auth::id() ?? 0
            );

            return JsonResponse::success($escalade->toArray(), 'Escalade créée avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Prendre en charge une escalade
     */
    public function prendreEnChargeEscalade(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $this->serviceEscalade->prendreEnCharge($id, Auth::id() ?? 0);
            return JsonResponse::success(null, 'Escalade prise en charge');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Ajouter une action à une escalade
     */
    public function ajouterActionEscalade(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $typeAction = Request::input('type_action', '');
        $description = Request::input('description', '');

        if (empty($typeAction) || empty($description)) {
            return JsonResponse::error('Type et description requis');
        }

        try {
            $action = $this->serviceEscalade->ajouterAction(
                $id,
                Auth::id() ?? 0,
                $typeAction,
                $description
            );

            return JsonResponse::success($action->toArray(), 'Action ajoutée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Résoudre une escalade
     */
    public function resoudreEscalade(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $resolution = Request::input('resolution', '');

        if (empty($resolution)) {
            return JsonResponse::error('Résolution requise');
        }

        try {
            $this->serviceEscalade->resoudre($id, Auth::id() ?? 0, $resolution);
            return JsonResponse::success(null, 'Escalade résolue');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Escalader au niveau supérieur
     */
    public function escaladerNiveauSuperieur(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $motif = Request::input('motif', '');

        if (empty($motif)) {
            return JsonResponse::error('Motif requis');
        }

        try {
            $this->serviceEscalade->escaladerNiveauSuperieur($id, Auth::id() ?? 0, $motif);
            return JsonResponse::success(null, 'Escalade au niveau supérieur effectuée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Fermer une escalade
     */
    public function fermerEscalade(int $id): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $motif = Request::input('motif', '');

        if (empty($motif)) {
            return JsonResponse::error('Motif requis');
        }

        try {
            $this->serviceEscalade->fermer($id, Auth::id() ?? 0, $motif);
            return JsonResponse::success(null, 'Escalade fermée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Escalades assignées à l'utilisateur connecté
     */
    public function mesEscalades(): JsonResponse
    {
        $userId = Auth::id();
        if ($userId === null) {
            return JsonResponse::unauthorized();
        }

        $escalades = $this->serviceEscalade->getEscaladesAssigneesA($userId);

        $result = [];
        foreach ($escalades as $escalade) {
            $data = $escalade->toArray();
            $data['dossier'] = $escalade->dossier()?->toArray();
            $result[] = $data;
        }

        return JsonResponse::success($result);
    }

    /**
     * API: Statistiques des escalades
     */
    public function statistiquesEscalades(): JsonResponse
    {
        if (!$this->checkAccess('workflow', 'lire')) {
            return JsonResponse::forbidden();
        }

        $stats = $this->serviceEscalade->getStatistiques();
        return JsonResponse::success($stats);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Vérifie les permissions
     */
    private function checkAccess(string $ressource, string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, $ressource, $action);
    }
}
