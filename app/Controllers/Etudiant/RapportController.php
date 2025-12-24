<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use App\Models\DossierEtudiant;
use App\Models\RapportEtudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Rapport Étudiant
 */
class RapportController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/rapport.php';
        return Response::html((string) ob_get_clean());
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        $dossier = $etudiant ? DossierEtudiant::findByEtudiant($etudiant->getId()) : null;
        $rapport = $dossier ? RapportEtudiant::findByDossier($dossier->getId()) : null;
        return JsonResponse::success($rapport?->toArray());
    }

    public function store(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $data = Request::all();
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        $dossier = $etudiant ? DossierEtudiant::findByEtudiant($etudiant->getId()) : null;
        if ($dossier === null) {
            return JsonResponse::notFound('Dossier non trouvé');
        }
        $rapport = new RapportEtudiant(['dossier_id' => $dossier->getId(), ...$data]);
        $rapport->save();
        ServiceAudit::log('rapport_soumis', 'rapport', $rapport->getId());
        return JsonResponse::success($rapport->toArray(), 'Rapport enregistré');
    }
}
