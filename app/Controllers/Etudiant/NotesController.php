<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Models\Etudiant;
use App\Models\DossierEtudiant;
use App\Models\Soutenance;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Notes Étudiant
 */
class NotesController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/notes.php';
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
            return JsonResponse::notFound();
        }
        $dossier = DossierEtudiant::findByEtudiant($etudiant->getId());
        if ($dossier === null) {
            return JsonResponse::notFound('Dossier non trouvé');
        }
        $soutenance = Soutenance::findByDossier($dossier->getId());
        $notes = $soutenance?->getNotes() ?? [];
        return JsonResponse::success([
            'notes' => $notes,
            'mention' => $soutenance?->mention ?? null,
            'note_finale' => $soutenance?->note_finale ?? null,
        ]);
    }
}
