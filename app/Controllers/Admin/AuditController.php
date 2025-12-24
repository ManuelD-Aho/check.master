<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Audit Admin
 */
class AuditController
{
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
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.*, u.nom_utilisateur FROM pister p
                LEFT JOIN utilisateurs u ON p.utilisateur_id = u.id_utilisateur
                ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = Model::raw($sql, ['limit' => $limit, 'offset' => $offset]);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
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

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'audit', 'lire');
    }
}
