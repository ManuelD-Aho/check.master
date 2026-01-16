<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Services\Commission\ServiceCommission;
use App\Models\CommissionSession;
use App\Models\CommissionVote;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Sessions Commission
 * 
 * Gère les sessions de commission d'évaluation des rapports.
 * 
 * @see PRD 03 - Workflow & Commission
 */
class SessionsController
{
    private ServiceCommission $service;

    public function __construct()
    {
        $this->service = new ServiceCommission();
    }

    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }

        $sessions = CommissionSession::all();
        $stats = $this->service->getStatistiques();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/sessions.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $statut = Request::query('statut') ?: null;
        $page = max(1, (int) Request::query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];

        if ($statut !== null) {
            $conditions[] = 'statut = :statut';
            $params['statut'] = $statut;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM sessions_commission {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT * FROM sessions_commission {$whereClause} 
                ORDER BY date_session DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return JsonResponse::success([
            'sessions' => $rows,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $perPage),
                'perPage' => $perPage,
                'totalItems' => $total,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $session = CommissionSession::find($id);
        if ($session === null) {
            return JsonResponse::notFound('Session non trouvée');
        }

        $data = $session->toArray();
        $data['membres'] = array_map(fn($m) => $m->toArray(), $session->membres());
        $data['rapports'] = $this->getRapportsSession($id);

        return JsonResponse::success($data);
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }

        $data = Request::all();

        if (empty($data['date_session'])) {
            return JsonResponse::error('Date de session requise');
        }

        try {
            $session = $this->service->creerSession($data, Auth::id() ?? 0);
            return JsonResponse::success($session->toArray(), 'Session créée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    public function update(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $session = CommissionSession::find($id);
        if ($session === null) {
            return JsonResponse::notFound('Session non trouvée');
        }

        $data = Request::all();
        $session->fill($data);
        $session->save();

        ServiceAudit::log('modification_session', 'session_commission', $id);

        return JsonResponse::success($session->toArray(), 'Session mise à jour');
    }

    public function demarrer(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $this->service->demarrerSession($id, Auth::id() ?? 0);
            return JsonResponse::success(null, 'Session démarrée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    public function terminer(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $this->service->terminerSession($id, Auth::id() ?? 0);
            return JsonResponse::success(null, 'Session terminée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    public function annuler(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $session = CommissionSession::find($id);
        if ($session === null) {
            return JsonResponse::notFound('Session non trouvée');
        }

        $session->statut = 'Annulee';
        $session->save();

        ServiceAudit::log('annulation_session', 'session_commission', $id);

        return JsonResponse::success(null, 'Session annulée');
    }

    /**
     * Ajouter un membre à la session
     */
    public function ajouterMembre(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $enseignantId = (int) Request::input('enseignant_id', 0);
        if ($enseignantId === 0) {
            return JsonResponse::error('Enseignant requis');
        }

        try {
            $this->service->ajouterMembreSession($id, $enseignantId);
            return JsonResponse::success(null, 'Membre ajouté');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * Retirer un membre de la session
     */
    public function retirerMembre(int $id, int $membreId): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $this->service->retirerMembreSession($id, $membreId);
            return JsonResponse::success(null, 'Membre retiré');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * Enregistrer un vote
     */
    public function voter(int $id): JsonResponse
    {
        if (!$this->checkAccess('evaluer')) {
            return JsonResponse::forbidden();
        }

        $rapportId = (int) Request::input('rapport_id', 0);
        $decision = Request::input('decision', '');
        $commentaire = Request::input('commentaire', '');
        $tour = (int) Request::input('tour', 1);

        if ($rapportId === 0 || empty($decision)) {
            return JsonResponse::error('Rapport et décision requis');
        }

        // Récupérer l'enseignant membre connecté
        $membreId = $this->getMembreIdConnecte();
        if ($membreId === null) {
            return JsonResponse::forbidden('Vous n\'êtes pas membre de cette commission');
        }

        try {
            $vote = CommissionVote::voter($id, $rapportId, $membreId, $tour, $decision, $commentaire ?: null);
            return JsonResponse::success($vote->toArray(), 'Vote enregistré');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * Statistiques de la session
     */
    public function statistiques(int $id): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $session = CommissionSession::find($id);
        if ($session === null) {
            return JsonResponse::notFound('Session non trouvée');
        }

        $stats = $this->service->getStatistiquesSession($id);
        return JsonResponse::success($stats);
    }

    /**
     * Récupère les rapports d'une session avec leurs votes
     */
    private function getRapportsSession(int $sessionId): array
    {
        $sql = "SELECT re.*, de.id_dossier, e.nom_etu, e.prenom_etu
                FROM rapports_etudiants re
                INNER JOIN dossiers_etudiants de ON de.id_dossier = re.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE re.dossier_id IN (
                    SELECT dossier_id FROM rapports_sessions WHERE session_id = :session
                )
                ORDER BY re.date_soumission";

        $stmt = Model::raw($sql, ['session' => $sessionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère l'ID du membre enseignant connecté
     */
    private function getMembreIdConnecte(): ?int
    {
        $userId = Auth::id();
        if ($userId === null) {
            return null;
        }

        $sql = "SELECT e.id_enseignant 
                FROM enseignants e
                INNER JOIN utilisateurs u ON u.login_utilisateur = e.email_ens
                WHERE u.id_utilisateur = :id AND e.actif = 1";

        $stmt = Model::raw($sql, ['id' => $userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? (int) $result['id_enseignant'] : null;
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'commission', $action);
    }
}
