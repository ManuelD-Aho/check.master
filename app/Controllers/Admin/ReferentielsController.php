<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Référentiels Admin
 */
class ReferentielsController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/referentiels.php';
        return Response::html((string) ob_get_clean());
    }

    public function grades(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stmt = Model::raw("SELECT * FROM grades ORDER BY niveau_hierarchique");
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function specialites(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stmt = Model::raw("SELECT * FROM specialites WHERE actif = 1");
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function annees(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stmt = Model::raw("SELECT * FROM annee_academique ORDER BY date_debut DESC");
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function salles(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stmt = Model::raw("SELECT * FROM salles WHERE actif = 1");
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'configuration', 'lire');
    }
}
