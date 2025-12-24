<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Models\Etudiant;
use App\Models\DossierEtudiant;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Dashboard Étudiant
 */
class DashboardController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        $dossier = $etudiant ? DossierEtudiant::findByEtudiant($etudiant->getId()) : null;
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/dashboard.php';
        return Response::html((string) ob_get_clean());
    }

    public function stats(): JsonResponse
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
        $rapport = $dossier?->getRapport();
        $soutenance = $dossier?->getSoutenance();
        $stats = [
            'etat_dossier' => $dossier?->getEtatActuel()?->nom_etat ?? 'Non inscrit',
            'paiement_complet' => $dossier !== null && $dossier->paiementComplet(),
            'rapport_soumis' => $rapport !== null && in_array($rapport->statut ?? '', ['Soumis', 'Valide']),
            'soutenance_planifiee' => $soutenance !== null && $soutenance->statut === 'Planifiee',
        ];
        return JsonResponse::success($stats);
    }
}
