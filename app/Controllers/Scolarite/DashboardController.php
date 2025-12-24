<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Dashboard Scolarité
 */
class DashboardController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/dashboard.php';
        return Response::html((string) ob_get_clean());
    }

    public function stats(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $stats = [
            'candidatures_attente' => $this->count("SELECT COUNT(*) FROM candidatures WHERE validee_scolarite = 0"),
            'paiements_jour' => $this->count("SELECT COUNT(*) FROM paiements WHERE DATE(created_at) = CURDATE()"),
            'dossiers_actifs' => $this->count("SELECT COUNT(*) FROM dossiers_etudiants"),
            'penalites_actives' => $this->count("SELECT COUNT(*) FROM penalites WHERE payee = 0"),
        ];
        return JsonResponse::success($stats);
    }

    private function count(string $sql): int
    {
        try {
            $stmt = Model::raw($sql);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('Dashboard Scolarite SQL error: ' . $e->getMessage());
            return 0;
        }
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'candidatures', 'lire');
    }
}
