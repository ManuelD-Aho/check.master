<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\Candidature;
use App\Models\DossierEtudiant;
use App\Models\Etudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Candidature Étudiant
 */
class CandidatureController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/candidature.php';
        return Response::html((string) ob_get_clean());
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        if ($etudiant === null) {
            return JsonResponse::notFound('Étudiant non trouvé');
        }
        $dossier = DossierEtudiant::findByEtudiant($etudiant->getId());
        $candidature = $dossier ? Candidature::findByDossier($dossier->getId()) : null;
        return JsonResponse::success($candidature?->toArray());
    }

    public function store(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $data = Request::all();
        if (empty($data['theme'])) {
            return JsonResponse::validationError(['theme' => ['Le thème est requis']]);
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        if ($etudiant === null) {
            return JsonResponse::notFound();
        }
        $candidature = new Candidature($data);
        $candidature->save();
        ServiceAudit::log('candidature_soumise', 'candidature', $candidature->getId());
        return JsonResponse::success($candidature->toArray(), 'Candidature soumise');
    }
}
