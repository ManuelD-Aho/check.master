<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\CommissionSession;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Sessions Commission
 */
class SessionsController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/sessions.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $sessions = CommissionSession::all();
        return JsonResponse::success(array_map(fn($s) => $s->toArray(), $sessions));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $session = new CommissionSession($data);
        $session->save();
        ServiceAudit::log('session_creee', 'session_commission', $session->getId());
        return JsonResponse::success($session->toArray(), 'Session créée');
    }

    public function demarrer(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $session = CommissionSession::find($id);
        if ($session === null) {
            return JsonResponse::notFound();
        }
        $session->statut = 'En_cours';
        $session->save();
        ServiceAudit::log('session_demarree', 'session_commission', $id);
        return JsonResponse::success(null, 'Session démarrée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'commission', $action);
    }
}
