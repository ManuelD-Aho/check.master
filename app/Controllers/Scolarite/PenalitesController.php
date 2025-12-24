<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Penalite;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Pénalités Scolarité
 */
class PenalitesController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/penalites.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $penalites = Penalite::nonPayees();
        return JsonResponse::success(array_map(fn($p) => $p->toArray(), $penalites));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $penalite = new Penalite($data);
        $penalite->save();
        ServiceAudit::log('penalite_creee', 'penalite', $penalite->getId());
        return JsonResponse::success($penalite->toArray(), 'Pénalité créée');
    }

    public function payer(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $penalite = Penalite::find($id);
        if ($penalite === null) {
            return JsonResponse::notFound();
        }
        $penalite->payee = true;
        $penalite->date_paiement = date('Y-m-d');
        $penalite->save();
        ServiceAudit::log('penalite_payee', 'penalite', $id);
        return JsonResponse::success(null, 'Pénalité marquée payée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'penalites', $action);
    }
}
