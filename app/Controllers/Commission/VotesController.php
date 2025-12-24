<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\VoteCommission;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Votes Commission
 */
class VotesController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/votes.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(int $sessionId): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $votes = VoteCommission::findBySession($sessionId);
        return JsonResponse::success(array_map(fn($v) => $v->toArray(), $votes));
    }

    public function voter(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        if (empty($data['session_id']) || empty($data['rapport_id']) || empty($data['decision'])) {
            return JsonResponse::validationError(['decision' => ['Décision requise']]);
        }
        $vote = new VoteCommission([
            'session_id' => $data['session_id'],
            'rapport_id' => $data['rapport_id'],
            'membre_id' => Auth::id(),
            'tour' => $data['tour'] ?? 1,
            'decision' => $data['decision'],
            'commentaire' => $data['commentaire'] ?? null,
        ]);
        $vote->save();
        ServiceAudit::log('vote_enregistre', 'vote_commission', $vote->getId());
        return JsonResponse::success(null, 'Vote enregistré');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'commission', 'valider');
    }
}
