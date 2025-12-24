<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\Candidature;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Candidatures Scolarité
 */
class CandidaturesController
{
    private ServiceScolarite $service;

    public function __construct()
    {
        $this->service = new ServiceScolarite();
    }

    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/candidatures.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $candidatures = Candidature::enAttente();
        return JsonResponse::success(array_map(fn($c) => $c->toArray(), $candidatures));
    }

    public function valider(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $this->service->validerCandidature($id, Auth::id() ?? 0);
        return JsonResponse::success(null, 'Candidature validée');
    }

    public function rejeter(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $motif = Request::post('motif', '');
        $this->service->rejeterCandidature($id, $motif, Auth::id() ?? 0);
        return JsonResponse::success(null, 'Candidature rejetée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'candidatures', 'valider');
    }
}
