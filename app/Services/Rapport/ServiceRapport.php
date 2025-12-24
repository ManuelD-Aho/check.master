<?php

declare(strict_types=1);

namespace App\Services\Rapport;

use App\Models\RapportEtudiant;
use App\Models\DossierEtudiant;
use App\Services\Security\ServiceAudit;
use App\Services\Core\ServiceFichier;
use App\Services\Workflow\ServiceWorkflow;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;

/**
 * Service Rapport
 * 
 * Gestion des rapports étudiants: versioning, soumission, workflow.
 */
class ServiceRapport
{
    /**
     * Crée un nouveau rapport (première version)
     */
    public function creer(int $dossierId, array $donnees, int $creePar): RapportEtudiant
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        // Vérifier qu'un rapport n'existe pas déjà
        $rapportExistant = RapportEtudiant::firstWhere(['dossier_id' => $dossierId]);
        if ($rapportExistant !== null) {
            throw new ValidationException('Un rapport existe déjà pour ce dossier');
        }

        $rapport = new RapportEtudiant([
            'dossier_id' => $dossierId,
            'titre' => $donnees['titre'],
            'version' => 1,
            'statut' => 'Brouillon',
            'cree_par' => $creePar,
        ]);
        $rapport->save();

        ServiceAudit::logCreation('rapport', $rapport->getId(), $donnees);

        return $rapport;
    }

    /**
     * Crée une nouvelle version du rapport
     */
    public function creerVersion(int $rapportId, array $donnees, int $creePar): RapportEtudiant
    {
        $rapportOriginal = RapportEtudiant::find($rapportId);
        if ($rapportOriginal === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        $nouvelleVersion = new RapportEtudiant([
            'dossier_id' => $rapportOriginal->dossier_id,
            'titre' => $donnees['titre'] ?? $rapportOriginal->titre,
            'version' => ((int) $rapportOriginal->version) + 1,
            'statut' => 'Brouillon',
            'cree_par' => $creePar,
        ]);
        $nouvelleVersion->save();

        ServiceAudit::logCreation('rapport_version', $nouvelleVersion->getId(), [
            'rapport_original_id' => $rapportId,
            'nouvelle_version' => $nouvelleVersion->version,
        ]);

        return $nouvelleVersion;
    }

    /**
     * Soumet un rapport pour évaluation
     */
    public function soumettre(int $rapportId, int $utilisateurId): bool
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        if ($rapport->statut !== 'Brouillon' && $rapport->statut !== 'A_revoir') {
            throw new ValidationException('Ce rapport ne peut pas être soumis');
        }

        $rapport->statut = 'Soumis';
        $rapport->soumis_le = date('Y-m-d H:i:s');
        $rapport->save();

        ServiceAudit::log('soumission_rapport', 'rapport', $rapportId);

        // Avancer le workflow du dossier
        if ($rapport->dossier_id !== null) {
            $this->avancerWorkflow((int) $rapport->dossier_id, $utilisateurId);
        }

        return true;
    }

    /**
     * Avance le workflow après soumission
     */
    private function avancerWorkflow(int $dossierId, int $utilisateurId): void
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            return;
        }

        $etat = $dossier->getEtatActuel();
        if ($etat === null) {
            return;
        }

        // Si on est en phase candidature validée, passer au filtre communication
        if ($etat->code_etat === 'CANDIDATURE_SOUMISE') {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    $dossierId,
                    'VERIFICATION_SCOLARITE',
                    $utilisateurId,
                    'Rapport soumis'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition rapport: ' . $e->getMessage());
            }
        }
    }

    /**
     * Attache un fichier au rapport
     */
    public function attacherFichier(int $rapportId, array $fichier, int $utilisateurId): array
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        $resultat = ServiceFichier::upload(
            $fichier,
            'rapport',
            'rapports/' . date('Y/m'),
            $utilisateurId
        );

        $rapport->chemin_fichier = $resultat['path'];
        $rapport->nom_fichier = $resultat['name'];
        $rapport->hash_fichier = $resultat['hash'];
        $rapport->save();

        ServiceAudit::log('upload_rapport', 'rapport', $rapportId, [
            'fichier' => $resultat['name'],
        ]);

        return $resultat;
    }

    /**
     * Retourne l'historique des versions d'un rapport
     */
    public function getHistoriqueVersions(int $dossierId): array
    {
        $sql = "SELECT * FROM rapports_etudiants 
                WHERE dossier_id = :dossier 
                ORDER BY version DESC";

        $stmt = \App\Orm\Model::raw($sql, ['dossier' => $dossierId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour le statut d'un rapport
     */
    public function mettreAJourStatut(int $rapportId, string $statut, int $utilisateurId): bool
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        $ancienStatut = $rapport->statut;
        $rapport->statut = $statut;
        $rapport->save();

        ServiceAudit::log('changement_statut_rapport', 'rapport', $rapportId, [
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $statut,
        ]);

        return true;
    }
}
