<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Paiement;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use App\Services\Security\ServiceAudit;
use App\Services\Documents\ServicePdf;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;
use App\Orm\Model;

/**
 * Service Paiement
 * 
 * Gestion complète des paiements de scolarité.
 * Génération automatique des reçus PDF.
 * 
 * @see PRD 07 - Financier
 */
class ServicePaiement
{
    /**
     * Modes de paiement autorisés
     */
    public const MODE_ESPECES = 'Especes';
    public const MODE_CARTE = 'Carte';
    public const MODE_VIREMENT = 'Virement';
    public const MODE_CHEQUE = 'Cheque';
    public const MODE_MOBILE = 'Mobile';

    /**
     * Enregistre un nouveau paiement
     */
    public function enregistrer(array $donnees, int $enregistrePar): Paiement
    {
        // Valider les données
        $this->validerDonneesPaiement($donnees);

        // Vérifier que l'étudiant existe
        $etudiant = Etudiant::find($donnees['etudiant_id']);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        // Générer la référence unique
        $reference = $this->genererReference();

        // Créer le paiement
        $paiement = new Paiement([
            'etudiant_id' => $donnees['etudiant_id'],
            'annee_acad_id' => $donnees['annee_acad_id'],
            'montant_paye' => $donnees['montant'],
            'mode_paiement' => $donnees['mode_paiement'] ?? self::MODE_ESPECES,
            'reference' => $reference,
            'date_paiement' => $donnees['date_paiement'] ?? date('Y-m-d H:i:s'),
            'statut' => 'Valide',
            'motif' => $donnees['motif'] ?? 'Frais de scolarité',
            'enregistre_par' => $enregistrePar,
        ]);
        $paiement->save();

        // Journaliser
        ServiceAudit::logCreation('paiement', $paiement->getId(), [
            'etudiant_id' => $donnees['etudiant_id'],
            'montant' => $donnees['montant'],
            'reference' => $reference,
        ]);

        // Générer le reçu PDF
        $this->genererRecu($paiement, $etudiant);

        // Notifier l'étudiant
        $this->notifierEtudiant($paiement, $etudiant);

        return $paiement;
    }

    /**
     * Valide les données de paiement
     */
    private function validerDonneesPaiement(array $donnees): void
    {
        if (empty($donnees['etudiant_id'])) {
            throw new ValidationException('L\'identifiant étudiant est requis');
        }

        if (empty($donnees['annee_acad_id'])) {
            throw new ValidationException('L\'année académique est requise');
        }

        if (empty($donnees['montant']) || (float) $donnees['montant'] <= 0) {
            throw new ValidationException('Le montant doit être supérieur à 0');
        }

        $modesValides = [
            self::MODE_ESPECES,
            self::MODE_CARTE,
            self::MODE_VIREMENT,
            self::MODE_CHEQUE,
            self::MODE_MOBILE,
        ];

        if (!empty($donnees['mode_paiement']) && !in_array($donnees['mode_paiement'], $modesValides, true)) {
            throw new ValidationException('Mode de paiement invalide');
        }
    }

    /**
     * Génère une référence unique de paiement
     */
    private function genererReference(): string
    {
        $prefix = 'PAY';
        $annee = date('Y');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$annee}-{$random}";
    }

    /**
     * Génère le reçu PDF du paiement
     */
    private function genererRecu(Paiement $paiement, Etudiant $etudiant): void
    {
        $donnees = [
            'numero_recu' => $paiement->reference,
            'date' => date('d/m/Y', strtotime($paiement->date_paiement ?? 'now')),
            'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
            'matricule' => $etudiant->numero_carte ?? '',
            'annee_academique' => $this->getLibelleAnnee((int) $paiement->annee_acad_id),
            'motif' => $paiement->motif ?? 'Frais de scolarité',
            'mode_paiement' => $paiement->mode_paiement ?? 'Espèces',
            'montant' => number_format((float) $paiement->montant_paye, 0, ',', ' '),
            'hash' => '',
        ];

        try {
            $resultat = ServicePdf::generer(
                ServicePdf::TYPE_RECU_PAIEMENT,
                $donnees,
                (int) ($paiement->enregistre_par ?? 0),
                'paiement',
                $paiement->getId()
            );

            // Mettre à jour le paiement avec le chemin du reçu
            $paiement->chemin_recu = $resultat['path'];
            $paiement->recu_genere = true;
            $paiement->save();
        } catch (\Exception $e) {
            error_log('Erreur génération reçu: ' . $e->getMessage());
        }
    }

    /**
     * Retourne le libellé de l'année académique
     */
    private function getLibelleAnnee(int $anneeId): string
    {
        $annee = AnneeAcademique::find($anneeId);
        return $annee !== null ? ($annee->libelle_annee_acad ?? '') : '';
    }

    /**
     * Notifie l'étudiant du paiement
     */
    private function notifierEtudiant(Paiement $paiement, Etudiant $etudiant): void
    {
        if ($etudiant->utilisateur_id === null) {
            return;
        }

        ServiceNotification::envoyerParCode(
            'paiement_recu',
            [(int) $etudiant->utilisateur_id],
            [
                'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                'montant' => number_format((float) $paiement->montant_paye, 0, ',', ' '),
                'reference' => $paiement->reference,
                'date' => date('d/m/Y', strtotime($paiement->date_paiement ?? 'now')),
            ]
        );
    }

    /**
     * Retourne l'historique des paiements d'un étudiant
     */
    public function getHistorique(int $etudiantId, ?int $anneeAcadId = null): array
    {
        $sql = "SELECT p.*, aa.libelle_annee_acad
                FROM paiements p
                LEFT JOIN annees_academiques aa ON aa.id_annee_acad = p.annee_acad_id
                WHERE p.etudiant_id = :etudiant";
        
        $params = ['etudiant' => $etudiantId];

        if ($anneeAcadId !== null) {
            $sql .= " AND p.annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $sql .= " ORDER BY p.date_paiement DESC";

        $stmt = Model::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcule le solde dû par un étudiant
     */
    public function calculerSolde(int $etudiantId, int $anneeAcadId): array
    {
        // Récupérer le montant total à payer
        $sql = "SELECT COALESCE(montant, 0) as montant_du 
                FROM frais_scolarite WHERE annee_acad_id = :annee";
        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $frais = $stmt->fetch(\PDO::FETCH_ASSOC);
        $montantDu = (float) ($frais['montant_du'] ?? 0);

        // Récupérer le total payé
        $sql = "SELECT COALESCE(SUM(montant_paye), 0) as total_paye
                FROM paiements 
                WHERE etudiant_id = :etudiant 
                AND annee_acad_id = :annee 
                AND statut = 'Valide'";
        $stmt = Model::raw($sql, ['etudiant' => $etudiantId, 'annee' => $anneeAcadId]);
        $paiements = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalPaye = (float) ($paiements['total_paye'] ?? 0);

        // Récupérer les exonérations
        $serviceExoneration = new ServiceExoneration();
        $exonerations = $serviceExoneration->getTotalExonerations($etudiantId, $anneeAcadId);

        // Récupérer les pénalités
        $servicePenalite = new ServicePenalite();
        $penalites = $servicePenalite->getTotalPenalites($etudiantId, $anneeAcadId);

        // Calculer le solde: Dû - Payé - Exonérations + Pénalités
        $solde = $montantDu - $totalPaye - $exonerations + $penalites;

        return [
            'montant_du' => $montantDu,
            'total_paye' => $totalPaye,
            'exonerations' => $exonerations,
            'penalites' => $penalites,
            'solde_restant' => max(0, $solde),
            'est_complet' => $solde <= 0,
            'trop_percu' => $solde < 0 ? abs($solde) : 0,
        ];
    }

    /**
     * Vérifie si le paiement est complet
     */
    public function estComplet(int $etudiantId, int $anneeAcadId): bool
    {
        $solde = $this->calculerSolde($etudiantId, $anneeAcadId);
        return $solde['est_complet'];
    }

    /**
     * Annule un paiement
     */
    public function annuler(int $paiementId, string $motif, int $annulePar): bool
    {
        $paiement = Paiement::find($paiementId);
        if ($paiement === null) {
            throw new NotFoundException('Paiement non trouvé');
        }

        $paiement->statut = 'Annule';
        $paiement->motif_annulation = $motif;
        $paiement->annule_par = $annulePar;
        $paiement->annule_le = date('Y-m-d H:i:s');
        $paiement->save();

        ServiceAudit::log('annulation_paiement', 'paiement', $paiementId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Retourne les statistiques de paiements
     */
    public function getStatistiques(int $anneeAcadId): array
    {
        $sql = "SELECT 
                    COUNT(DISTINCT etudiant_id) as nombre_etudiants,
                    SUM(CASE WHEN statut = 'Valide' THEN montant_paye ELSE 0 END) as total_encaisse,
                    COUNT(*) as nombre_paiements,
                    AVG(CASE WHEN statut = 'Valide' THEN montant_paye ELSE NULL END) as moyenne_paiement
                FROM paiements 
                WHERE annee_acad_id = :annee";

        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Statistiques par mode de paiement
        $sql2 = "SELECT mode_paiement, COUNT(*) as nombre, SUM(montant_paye) as total
                 FROM paiements 
                 WHERE annee_acad_id = :annee AND statut = 'Valide'
                 GROUP BY mode_paiement";

        $stmt2 = Model::raw($sql2, ['annee' => $anneeAcadId]);
        $parMode = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'nombre_etudiants' => (int) ($stats['nombre_etudiants'] ?? 0),
            'total_encaisse' => (float) ($stats['total_encaisse'] ?? 0),
            'nombre_paiements' => (int) ($stats['nombre_paiements'] ?? 0),
            'moyenne_paiement' => round((float) ($stats['moyenne_paiement'] ?? 0), 2),
            'par_mode' => $parMode,
        ];
    }

    /**
     * Télécharge le reçu d'un paiement
     */
    public function getRecu(int $paiementId): ?string
    {
        $paiement = Paiement::find($paiementId);
        if ($paiement === null || empty($paiement->chemin_recu)) {
            return null;
        }

        return $paiement->chemin_recu;
    }

    /**
     * Régénère un reçu
     */
    public function regenererRecu(int $paiementId, int $regenerePar): array
    {
        $paiement = Paiement::find($paiementId);
        if ($paiement === null) {
            throw new NotFoundException('Paiement non trouvé');
        }

        $etudiant = Etudiant::find((int) $paiement->etudiant_id);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        $donnees = [
            'numero_recu' => $paiement->reference,
            'date' => date('d/m/Y', strtotime($paiement->date_paiement ?? 'now')),
            'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
            'matricule' => $etudiant->numero_carte ?? '',
            'annee_academique' => $this->getLibelleAnnee((int) $paiement->annee_acad_id),
            'motif' => $paiement->motif ?? 'Frais de scolarité',
            'mode_paiement' => $paiement->mode_paiement ?? 'Espèces',
            'montant' => number_format((float) $paiement->montant_paye, 0, ',', ' '),
            'hash' => '',
            'regenere' => 'Régénéré le ' . date('d/m/Y H:i'),
        ];

        $resultat = ServicePdf::generer(
            ServicePdf::TYPE_RECU_PAIEMENT,
            $donnees,
            $regenerePar,
            'paiement',
            $paiement->getId()
        );

        $paiement->chemin_recu = $resultat['path'];
        $paiement->save();

        ServiceAudit::log('regeneration_recu', 'paiement', $paiementId);

        return $resultat;
    }
}
