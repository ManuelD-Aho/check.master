<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\SessionCommission;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur PV Commission
 */
class PvController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/pv.php';
        return Response::html((string) ob_get_clean());
    }

    public function generer(int $sessionId): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $session = SessionCommission::find($sessionId);
        if ($session === null) {
            return JsonResponse::notFound();
        }
        $session->pv_genere = true;
        $session->pv_chemin = '/storage/pv/session_' . $sessionId . '.pdf';
        $session->save();
        ServiceAudit::log('pv_genere', 'session_commission', $sessionId);
        return JsonResponse::success(['chemin' => $session->pv_chemin], 'PV généré');
    }

    public function telecharger(int $sessionId): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        $session = SessionCommission::find($sessionId);
        if ($session === null || !$session->pv_genere) {
            return Response::redirect('/commission/pv');
        }
        return Response::redirect($session->pv_chemin ?? '/');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'documents', 'lire');
    }
}
