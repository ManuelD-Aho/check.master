<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\Paiement;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Paiements Scolarité
 */
class PaiementsController
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
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/paiements.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $paiements = Paiement::all();
        return JsonResponse::success(array_map(fn($p) => $p->toArray(), $paiements));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        if (empty($data['etudiant_id']) || empty($data['montant'])) {
            return JsonResponse::validationError(['montant' => ['Montant requis']]);
        }
        $paiement = $this->service->enregistrerPaiement($data, Auth::id() ?? 0);
        return JsonResponse::success($paiement->toArray(), 'Paiement enregistré');
    }

    public function recap(int $etudiantId): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $anneeId = (int) Request::query('annee', 1);
        $recap = $this->service->getRecapPaiements($etudiantId, $anneeId);
        return JsonResponse::success($recap);
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'paiements', $action);
    }
}
