<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\JuryMembre;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Jury Soutenance
 */
class JuryController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/soutenance/jury.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $jurys = JuryMembre::all();
        return JsonResponse::success(array_map(fn($j) => $j->toArray(), $jurys));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $membre = new JuryMembre($data);
        $membre->save();
        ServiceAudit::log('jury_membre_ajoute', 'jury_membre', $membre->getId());
        return JsonResponse::success($membre->toArray(), 'Membre ajouté au jury');
    }

    public function accepter(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $membre = JuryMembre::find($id);
        if ($membre === null) {
            return JsonResponse::notFound();
        }
        $membre->statut_acceptation = 'Accepte';
        $membre->date_reponse = date('Y-m-d H:i:s');
        $membre->save();
        ServiceAudit::log('jury_acceptation', 'jury_membre', $id);
        return JsonResponse::success(null, 'Participation acceptée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'jury', $action);
    }
}
