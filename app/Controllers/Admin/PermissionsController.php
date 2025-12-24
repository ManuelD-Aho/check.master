<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Permission;
use App\Models\Ressource;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Permissions Admin
 */
class PermissionsController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/permissions.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $permissions = Permission::all();
        return JsonResponse::success(array_map(fn($p) => $p->toArray(), $permissions));
    }

    public function ressources(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $ressources = Ressource::all();
        return JsonResponse::success(array_map(fn($r) => $r->toArray(), $ressources));
    }

    public function update(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $permission = Permission::find($id);
        if ($permission === null) {
            return JsonResponse::notFound();
        }
        $data = Request::all();
        $permission->fill($data);
        $permission->save();
        ServicePermissions::invaliderToutCache();
        ServiceAudit::log('permission_modifiee', 'permission', $id);
        return JsonResponse::success(null, 'Permission modifiée');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'permissions', $action);
    }
}
