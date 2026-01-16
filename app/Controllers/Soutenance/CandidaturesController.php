<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Soutenance\ServiceCandidature;
use Src\Http\Response;
use Src\Http\Request;
use Src\Exceptions\ValidationException;

/**
 * Contrôleur des candidatures (Soutenance)
 * 
 * Gestion API des candidatures de stage/mémoire.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class CandidaturesController
{
    private ServiceCandidature $serviceCandidature;

    public function __construct()
    {
        $this->serviceCandidature = new ServiceCandidature();
    }

    /**
     * Liste les candidatures (API)
     */
    public function list(): Response
    {
        $filtres = [
            'statut' => Request::get('statut'),
            'annee_id' => Request::get('annee_id'),
            'recherche' => Request::get('q'),
        ];

        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $candidatures = $this->serviceCandidature->lister($filtres, $page, $perPage);

        return Response::json([
            'success' => true,
            'data' => $candidatures,
        ]);
    }

    /**
     * Affiche une candidature
     */
    public function show(int $id): Response
    {
        try {
            $details = $this->serviceCandidature->getDetails($id);

            return Response::json([
                'success' => true,
                'data' => $details,
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Crée une candidature
     */
    public function store(): Response
    {
        $dossierId = (int) Request::post('dossier_id');
        $donnees = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $candidature = $this->serviceCandidature->creer($dossierId, $donnees, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Candidature créée avec succès',
                'data' => [
                    'id' => $candidature->getId(),
                    'reference' => $candidature->reference,
                ],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Met à jour une candidature
     */
    public function update(int $id): Response
    {
        $donnees = Request::all();
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $candidature = $this->serviceCandidature->mettreAJour($id, $donnees, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Candidature mise à jour',
                'data' => $candidature->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Soumet une candidature
     */
    public function soumettre(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $candidature = $this->serviceCandidature->soumettre($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Candidature soumise avec succès',
                'data' => $candidature->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Valide une candidature
     */
    public function valider(int $id): Response
    {
        $commentaire = Request::post('commentaire');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $candidature = $this->serviceCandidature->valider($id, (int) $utilisateurId, $commentaire);

            return Response::json([
                'success' => true,
                'message' => 'Candidature validée',
                'data' => $candidature->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Rejette une candidature
     */
    public function rejeter(int $id): Response
    {
        $motif = Request::post('motif');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($motif)) {
            return Response::json(['error' => 'Le motif de rejet est requis'], 422);
        }

        try {
            $candidature = $this->serviceCandidature->rejeter($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Candidature rejetée',
                'data' => $candidature->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Demande des compléments
     */
    public function demanderComplements(int $id): Response
    {
        $demande = Request::post('demande');
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($demande)) {
            return Response::json(['error' => 'La demande de compléments est requise'], 422);
        }

        try {
            $candidature = $this->serviceCandidature->demanderComplements($id, $demande, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Demande de compléments envoyée',
                'data' => $candidature->toArray(),
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne les statistiques
     */
    public function statistiques(): Response
    {
        $anneeId = Request::get('annee_id');
        $stats = $this->serviceCandidature->getStatistiques(
            $anneeId ? (int) $anneeId : null
        );

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
