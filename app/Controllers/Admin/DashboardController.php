<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Dashboard Admin
 */
class DashboardController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/dashboard.php';
        return Response::html((string) ob_get_clean());
    }

    public function stats(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stats = [
            'utilisateurs_actifs' => $this->count("SELECT COUNT(*) FROM utilisateurs WHERE statut_utilisateur = 'Actif'"),
            'sessions_actives' => $this->count("SELECT COUNT(*) FROM sessions_actives WHERE expire_a > NOW()"),
            'etudiants_total' => $this->count("SELECT COUNT(*) FROM etudiants WHERE actif = 1"),
            'soutenances_mois' => $this->count("SELECT COUNT(*) FROM soutenances WHERE MONTH(date_soutenance) = MONTH(NOW())"),
            'alertes_sla' => $this->count("SELECT COUNT(*) FROM workflow_alertes WHERE envoyee = 0"),
        ];
        return JsonResponse::success($stats);
    }

    private function count(string $sql): int
    {
        try {
            $stmt = Model::raw($sql);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('Admin Dashboard SQL error: ' . $e->getMessage());
            return 0;
        }
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::estAdministrateur($userId);
    }
}
