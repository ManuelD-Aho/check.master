<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Soutenance;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Convocations Soutenance
 */
class ConvocationsController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/soutenance/convocations.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $soutenances = Soutenance::planifiees();
        return JsonResponse::success(array_map(fn($s) => $s->toArray(), $soutenances));
    }

    public function generer(int $soutenanceId): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            return JsonResponse::notFound();
        }
        $chemin = '/storage/convocations/soutenance_' . $soutenanceId . '.pdf';
        ServiceAudit::log('convocation_generee', 'soutenance', $soutenanceId);
        return JsonResponse::success(['chemin' => $chemin], 'Convocation générée');
    }

    public function envoyer(int $soutenanceId): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        ServiceAudit::log('convocation_envoyee', 'soutenance', $soutenanceId);
        return JsonResponse::success(null, 'Convocations envoyées');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'soutenances', 'lire');
    }
}
