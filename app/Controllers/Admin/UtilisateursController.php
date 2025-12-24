<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Utilisateur;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Utilisateurs Admin
 */
class UtilisateursController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/utilisateurs.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $utilisateurs = Utilisateur::all();
        return JsonResponse::success(array_map(fn($u) => $u->toArray(), $utilisateurs));
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $utilisateur = new Utilisateur($data);
        $utilisateur->save();
        ServiceAudit::log('utilisateur_cree', 'utilisateur', $utilisateur->getId());
        return JsonResponse::success($utilisateur->toArray(), 'Utilisateur créé');
    }

    public function update(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound();
        }
        $data = Request::all();
        $utilisateur->fill($data);
        $utilisateur->save();
        ServiceAudit::log('utilisateur_modifie', 'utilisateur', $id);
        return JsonResponse::success($utilisateur->toArray(), 'Utilisateur modifié');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'utilisateurs', $action);
    }
}
