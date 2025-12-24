<?php

declare(strict_types=1);

namespace App\Services\Scolarite;

use App\Models\Etudiant;
use App\Models\DossierEtudiant;
use App\Models\Candidature;
use App\Models\Paiement;
use App\Models\AnneeAcademique;
use App\Services\Security\ServiceAudit;
use App\Services\Workflow\ServiceWorkflow;
use Src\Exceptions\ValidationException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Scolarité
 * 
 * Gestion académique: étudiants, paiements, candidatures.
 * Implémente le gate de vérification de paiement.
 */
class ServiceScolarite
{
    /**
     * Crée un nouvel étudiant
     */
    public function creerEtudiant(array $donnees, int $creePar): Etudiant
    {
        $etudiant = new Etudiant($donnees);
        $etudiant->save();

        ServiceAudit::logCreation('etudiant', $etudiant->getId(), $donnees);

        return $etudiant;
    }

    /**
     * Crée un dossier étudiant pour une année académique
     */
    public function creerDossier(int $etudiantId, int $anneeAcadId): DossierEtudiant
    {
        // Vérifier que l'étudiant existe
        $etudiant = Etudiant::find($etudiantId);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        // Vérifier qu'un dossier n'existe pas déjà
        $dossierExistant = DossierEtudiant::trouver($etudiantId, $anneeAcadId);
        if ($dossierExistant !== null) {
            throw new ValidationException('Un dossier existe déjà pour cette année');
        }

        // Créer le dossier à l'état initial
        $etatInitial = \App\Models\WorkflowEtat::initial();

        $dossier = new DossierEtudiant([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
            'etat_actuel_id' => $etatInitial?->getId(),
            'date_entree_etat' => date('Y-m-d H:i:s'),
        ]);
        $dossier->save();

        ServiceAudit::logCreation('dossier_etudiant', $dossier->getId(), [
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
        ]);

        return $dossier;
    }

    /**
     * Enregistre un paiement
     */
    public function enregistrerPaiement(array $donnees, int $creePar): Paiement
    {
        $paiement = new Paiement($donnees);
        $paiement->statut = 'Valide';
        $paiement->enregistre_par = $creePar;
        $paiement->save();

        ServiceAudit::logCreation('paiement', $paiement->getId(), $donnees);

        // Vérifier si le paiement permet de débloquer le workflow
        $this->verifierDeblocagePaiement((int) $donnees['etudiant_id'], (int) $donnees['annee_acad_id']);

        return $paiement;
    }

    /**
     * Vérifie si le paiement complet débloque le workflow
     */
    private function verifierDeblocagePaiement(int $etudiantId, int $anneeAcadId): void
    {
        if (!$this->paiementComplet($etudiantId, $anneeAcadId)) {
            return;
        }

        // Le paiement est complet, débloquer les dossiers en attente
        $dossier = DossierEtudiant::trouver($etudiantId, $anneeAcadId);
        if ($dossier === null) {
            return;
        }

        $etat = $dossier->getEtatActuel();
        if ($etat !== null && $etat->code_etat === 'VERIFICATION_SCOLARITE') {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    $dossier->getId(),
                    'FILTRE_COMMUNICATION',
                    0,
                    'Paiement vérifié complet'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition paiement: ' . $e->getMessage());
            }
        }
    }

    /**
     * Vérifie si le paiement est complet pour un étudiant
     */
    public function paiementComplet(int $etudiantId, int $anneeAcadId): bool
    {
        $sql = "SELECT 
                    COALESCE(SUM(p.montant_paye), 0) as total_paye,
                    COALESCE(f.montant, 0) as montant_du
                FROM paiements p
                LEFT JOIN frais_scolarite f ON f.annee_acad_id = :annee
                WHERE p.etudiant_id = :etudiant 
                AND p.annee_acad_id = :annee2
                AND p.statut = 'Valide'";

        $stmt = Model::raw($sql, [
            'etudiant' => $etudiantId,
            'annee' => $anneeAcadId,
            'annee2' => $anneeAcadId,
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result || (float) $result['montant_du'] === 0.0) {
            return true; // Pas de frais configurés
        }

        return (float) $result['total_paye'] >= (float) $result['montant_du'];
    }

    /**
     * Valide une candidature
     */
    public function validerCandidature(int $candidatureId, int $validePar): bool
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        $candidature->statut = 'Validee';
        $candidature->validee_par = $validePar;
        $candidature->validee_le = date('Y-m-d H:i:s');
        $candidature->save();

        ServiceAudit::log('validation_candidature', 'candidature', $candidatureId);

        // Avancer le workflow
        if ($candidature->dossier_id !== null) {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    (int) $candidature->dossier_id,
                    'VERIFICATION_SCOLARITE',
                    $validePar,
                    'Candidature validée'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition candidature: ' . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Rejette une candidature
     */
    public function rejeterCandidature(int $candidatureId, string $motif, int $rejetePar): bool
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        $candidature->statut = 'Rejetee';
        $candidature->motif_rejet = $motif;
        $candidature->save();

        ServiceAudit::log('rejet_candidature', 'candidature', $candidatureId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Retourne le récapitulatif des paiements d'un étudiant
     */
    public function getRecapPaiements(int $etudiantId, int $anneeAcadId): array
    {
        $sql = "SELECT * FROM paiements 
                WHERE etudiant_id = :etudiant AND annee_acad_id = :annee
                ORDER BY date_paiement DESC";

        $stmt = Model::raw($sql, [
            'etudiant' => $etudiantId,
            'annee' => $anneeAcadId,
        ]);

        $paiements = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $total = array_reduce($paiements, function ($sum, $p) {
            return $sum + (float) ($p['montant_paye'] ?? 0);
        }, 0.0);

        return [
            'paiements' => $paiements,
            'total_paye' => $total,
            'complet' => $this->paiementComplet($etudiantId, $anneeAcadId),
        ];
    }

    /**
     * Recherche des étudiants
     */
    public function rechercherEtudiants(string $terme, int $limite = 50): array
    {
        return Etudiant::rechercher($terme, $limite);
    }
}
