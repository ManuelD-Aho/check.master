<?php

declare(strict_types=1);

namespace App\Controllers\Commission;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\RapportEtudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Évaluations Commission
 */
class EvaluationsController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/commission/evaluations.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $rapports = RapportEtudiant::enAttente();
        return JsonResponse::success(array_map(fn($r) => $r->toArray(), $rapports));
    }

    public function evaluer(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $rapport = RapportEtudiant::find($id);
        if ($rapport === null) {
            return JsonResponse::notFound();
        }
        $rapport->statut = $data['decision'] ?? 'En_evaluation';
        $rapport->save();
        ServiceAudit::log('rapport_evalue', 'rapport', $id, $data);
        return JsonResponse::success(null, 'Évaluation enregistrée');
    }

    public function annoter(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $annotation = Request::post('annotation', '');
        ServiceAudit::log('rapport_annote', 'rapport', $id, ['annotation' => $annotation]);
        return JsonResponse::success(null, 'Annotation ajoutée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'rapports', 'valider');
    }
}
