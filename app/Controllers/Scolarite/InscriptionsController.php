<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\DossierEtudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Inscriptions Scolarité
 */
class InscriptionsController
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
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/inscriptions.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $dossiers = DossierEtudiant::all();
        return JsonResponse::success(array_map(fn($d) => $d->toArray(), $dossiers));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $etudiantId = (int) ($data['etudiant_id'] ?? 0);
        $anneeId = (int) ($data['annee_acad_id'] ?? 0);
        if ($etudiantId <= 0 || $anneeId <= 0) {
            return JsonResponse::validationError(['etudiant_id' => ['Étudiant requis']]);
        }
        $dossier = $this->service->creerDossier($etudiantId, $anneeId);
        ServiceAudit::log('inscription_creee', 'dossier', $dossier->getId());
        return JsonResponse::success($dossier->toArray(), 'Inscription créée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'dossiers', 'creer');
    }
}
