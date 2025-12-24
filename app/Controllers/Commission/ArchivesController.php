<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Archives Commission
 */
class ArchivesController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/archives.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT s.*, COUNT(v.id_vote) as nb_votes 
                FROM sessions_commission s
                LEFT JOIN votes_commission v ON s.id_session = v.session_id
                WHERE s.statut = 'Terminee'
                GROUP BY s.id_session
                ORDER BY s.date_session DESC";
        $stmt = Model::raw($sql);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT * FROM sessions_commission WHERE id_session = :id";
        $stmt = Model::raw($sql, ['id' => $id]);
        $session = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $session ? JsonResponse::success($session) : JsonResponse::notFound();
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'archives', 'lire');
    }
}
