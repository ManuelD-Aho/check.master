<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Services\Finance\ServicePaiement;
use App\Services\Finance\ServicePenalite;
use App\Services\Finance\ServiceExoneration;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur finances étudiant
 * 
 * Dashboard financier pour l'étudiant.
 * 
 * @see PRD 07 - Financier
 */
class FinancesController
{
    private ServicePaiement $servicePaiement;
    private ServicePenalite $servicePenalite;
    private ServiceExoneration $serviceExoneration;

    public function __construct()
    {
        $this->servicePaiement = new ServicePaiement();
        $this->servicePenalite = new ServicePenalite();
        $this->serviceExoneration = new ServiceExoneration();
    }

    /**
     * Affiche la page finances de l'étudiant
     */
    public function index(): Response
    {
        return Response::view('modules/etudiant/finances/index');
    }

    /**
     * Retourne le résumé financier de l'étudiant
     */
    public function resume(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        // Récupérer l'étudiant associé
        $etudiant = Etudiant::firstWhere(['utilisateur_id' => $utilisateurId]);
        if ($etudiant === null) {
            return Response::json(['error' => 'Étudiant non trouvé'], 404);
        }

        $etudiantId = $etudiant->getId();
        $anneeId = Request::get('annee_id');

        if (!$anneeId) {
            $annee = AnneeAcademique::enCours();
            $anneeId = $annee?->getId();
        }

        if (!$anneeId) {
            return Response::json(['error' => 'Année académique non trouvée'], 404);
        }

        // Calculer le solde
        $solde = $this->servicePaiement->calculerSolde($etudiantId, (int) $anneeId);

        // Historique des paiements
        $paiements = $this->servicePaiement->getHistorique($etudiantId, (int) $anneeId);

        // Pénalités
        $penalites = $this->servicePenalite->getPenalites($etudiantId, (int) $anneeId);

        // Exonérations
        $exonerations = $this->serviceExoneration->getExonerations($etudiantId, (int) $anneeId);

        return Response::json([
            'success' => true,
            'data' => [
                'solde' => $solde,
                'paiements' => $paiements,
                'penalites' => $penalites,
                'exonerations' => $exonerations,
                'etudiant' => [
                    'id' => $etudiantId,
                    'nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                    'matricule' => $etudiant->numero_carte,
                ],
            ],
        ]);
    }
}
