<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Profil Étudiant
 */
class ProfilController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/profil.php';
        return Response::html((string) ob_get_clean());
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        return $etudiant ? JsonResponse::success($etudiant->toArray()) : JsonResponse::notFound();
    }

    public function update(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        if ($etudiant === null) {
            return JsonResponse::notFound();
        }
        $data = Request::all();
        $etudiant->telephone_etu = $data['telephone'] ?? $etudiant->telephone_etu;
        $etudiant->save();
        ServiceAudit::log('profil_update', 'etudiant', $etudiant->getId());
        return JsonResponse::success($etudiant->toArray(), 'Profil mis à jour');
    }
}
