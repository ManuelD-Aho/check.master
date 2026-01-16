<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Admin\ServiceAdministration;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Audit Admin
 * 
 * @see PRD 08 - Administration
 */
class AuditController
{
    private ServiceAdministration $service;

    public function __construct()
    {
        $this->service = new ServiceAdministration();
    }

    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/audit.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $action = Request::query('action') ?: null;
        $entite = Request::query('entite') ?: null;
        $utilisateurId = Request::query('utilisateur') ? (int) Request::query('utilisateur') : null;

        $dateDebut = null;
        $dateFin = null;

        if (!empty(Request::query('date_debut'))) {
            $dateDebut = \DateTime::createFromFormat('Y-m-d', Request::query('date_debut'));
        }
        if (!empty(Request::query('date_fin'))) {
            $dateFin = \DateTime::createFromFormat('Y-m-d', Request::query('date_fin'));
            if ($dateFin) {
                $dateFin->setTime(23, 59, 59);
            }
        }

        $data = $this->service->getLogsAudit($action, $entite, $utilisateurId, $dateDebut ?: null, $dateFin ?: null, $page, 50);

        return JsonResponse::success($data);
    }

    public function search(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }

        $action = Request::query('action', '');
        $entite = Request::query('entite', '');

        $sql = "SELECT p.*, u.nom_utilisateur FROM pister p
                LEFT JOIN utilisateurs u ON p.utilisateur_id = u.id_utilisateur
                WHERE (:action = '' OR p.action LIKE :action2)
                AND (:entite = '' OR p.entite_type = :entite2)
                ORDER BY p.created_at DESC LIMIT 100";

        $stmt = Model::raw($sql, [
            'action' => $action, 'action2' => "%$action%",
            'entite' => $entite, 'entite2' => $entite
        ]);

        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function statistiques(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }

        $stats = $this->service->statistiquesAudit();
        return JsonResponse::success($stats);
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'audit', 'lire');
    }
}
