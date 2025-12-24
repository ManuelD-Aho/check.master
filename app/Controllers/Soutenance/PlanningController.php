<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Soutenance;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Planning Soutenance
 */
class PlanningController
{
    private const STATUT_PLANIFIEE = 'Planifiee';
    private const STATUT_REPORTEE = 'Reportee';
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/soutenance/planning.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $soutenances = Soutenance::planifiees();
        return JsonResponse::success(array_map(fn($s) => $s->toArray(), $soutenances));
    }

    public function planifier(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        if (empty($data['dossier_id']) || empty($data['date_soutenance'])) {
            return JsonResponse::validationError(['date_soutenance' => ['Date de soutenance requise']]);
        }
        $soutenance = new Soutenance($data);
        $soutenance->statut = self::STATUT_PLANIFIEE;
        $soutenance->save();
        ServiceAudit::log('soutenance_planifiee', 'soutenance', $soutenance->getId());
        return JsonResponse::success($soutenance->toArray(), 'Soutenance planifiée');
    }

    public function reporter(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $nouvelleDate = Request::post('nouvelle_date', '');
        if (empty($nouvelleDate)) {
            return JsonResponse::validationError(['nouvelle_date' => ['Nouvelle date requise']]);
        }
        $soutenance = Soutenance::find($id);
        if ($soutenance === null) {
            return JsonResponse::notFound();
        }
        $soutenance->date_soutenance = $nouvelleDate;
        $soutenance->statut = self::STATUT_REPORTEE;
        $soutenance->save();
        ServiceAudit::log('soutenance_reportee', 'soutenance', $id);
        return JsonResponse::success(null, 'Soutenance reportée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'soutenances', $action);
    }
}
