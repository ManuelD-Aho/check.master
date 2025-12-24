<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Core\ServiceParametres;
use App\Services\Security\ServiceAudit;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Paramètres Admin
 */
class ParametresController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/parametres.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $params = ServiceParametres::all();
        return JsonResponse::success($params);
    }

    public function get(string $cle): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $valeur = ServiceParametres::get($cle);
        return JsonResponse::success(['cle' => $cle, 'valeur' => $valeur]);
    }

    public function update(): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }
        $cle = Request::post('cle', '');
        $valeur = Request::post('valeur', '');
        ServiceParametres::set($cle, $valeur);
        ServiceAudit::log('parametre_modifie', 'configuration', null, ['cle' => $cle]);
        return JsonResponse::success(null, 'Paramètre mis à jour');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'configuration', $action);
    }
}
