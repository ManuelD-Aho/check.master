<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\NoteSoutenance;
use App\Models\Soutenance;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Notes Soutenance
 */
class NotesController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/soutenance/notes.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(int $soutenanceId): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $notes = NoteSoutenance::findBySoutenance($soutenanceId);
        return JsonResponse::success(array_map(fn($n) => $n->toArray(), $notes));
    }

    public function saisir(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        if (empty($data['soutenance_id'])) {
            return JsonResponse::validationError(['soutenance_id' => ['Soutenance requise']]);
        }
        $note = new NoteSoutenance($data);
        $note->save();
        ServiceAudit::log('note_saisie', 'note_soutenance', $note->getId());
        return JsonResponse::success($note->toArray(), 'Note enregistrée');
    }

    public function finaliser(int $soutenanceId): JsonResponse
    {
        if (!$this->checkAccess('valider')) {
            return JsonResponse::forbidden();
        }
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            return JsonResponse::notFound();
        }
        $soutenance->statut = 'Terminee';
        $soutenance->save();
        ServiceAudit::log('soutenance_finalisee', 'soutenance', $soutenanceId);
        return JsonResponse::success(null, 'Soutenance finalisée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'notes', $action);
    }
}
