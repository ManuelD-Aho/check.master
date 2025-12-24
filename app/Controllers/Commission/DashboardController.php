<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Dashboard Commission
 */
class DashboardController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/dashboard.php';
        return Response::html((string) ob_get_clean());
    }

    public function stats(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stats = [
            'rapports_attente' => $this->count("SELECT COUNT(*) FROM rapports_etudiants WHERE statut = 'Soumis'"),
            'sessions_planifiees' => $this->count("SELECT COUNT(*) FROM sessions_commission WHERE statut = 'Planifiee'"),
            'rapports_evalues' => $this->count("SELECT COUNT(*) FROM rapports_etudiants WHERE statut = 'Valide'"),
        ];
        return JsonResponse::success($stats);
    }

    private function count(string $sql): int
    {
        try {
            $stmt = Model::raw($sql);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'commission', 'lire');
    }
}
