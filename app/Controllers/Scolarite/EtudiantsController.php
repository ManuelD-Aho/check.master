<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Étudiants Scolarité
 */
class EtudiantsController
{
    private ServiceScolarite $service;

    public function __construct()
    {
        $this->service = new ServiceScolarite();
    }

    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/etudiants.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $search = Request::query('q', '');
        $etudiants = $search ? $this->service->rechercherEtudiants($search) : Etudiant::all();
        return JsonResponse::success(array_map(fn($e) => $e->toArray(), $etudiants));
    }

    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $etudiant = Etudiant::find($id);
        return $etudiant ? JsonResponse::success($etudiant->toArray()) : JsonResponse::notFound();
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $etudiant = $this->service->creerEtudiant($data, Auth::id() ?? 0);
        return JsonResponse::success($etudiant->toArray(), 'Étudiant créé');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'etudiants', $action);
    }
}
