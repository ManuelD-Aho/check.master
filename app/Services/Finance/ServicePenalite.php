<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Penalite;
use App\Models\Etudiant;
use App\Services\Security\ServiceAudit;
use App\Services\Documents\ServicePdf;
use App\Services\Communication\ServiceNotification;
use App\Services\Core\ServiceParametres;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;
use App\Orm\Model;

/**
 * Service Pénalité
 * 
 * Calcul et gestion des pénalités de retard.
 * Taux configurables, plafonnement automatique.
 * 
 * @see PRD 07 - Financier
 */
class ServicePenalite
{
    /**
     * Paramètres par défaut
     */
    private const TAUX_JOUR_DEFAUT = 0.5; // 0.5% par jour
    private const PLAFOND_DEFAUT = 50; // Maximum 50% du montant dû
    private const JOURS_GRACE_DEFAUT = 7;

    /**
     * Crée une pénalité manuelle
     */
    public function creer(array $donnees, int $creePar): Penalite
    {
        // Valider les données
        if (empty($donnees['etudiant_id'])) {
            throw new ValidationException('L\'identifiant étudiant est requis');
        }

        if (empty($donnees['montant']) || (float) $donnees['montant'] <= 0) {
            throw new ValidationException('Le montant doit être supérieur à 0');
        }

        $etudiant = Etudiant::find($donnees['etudiant_id']);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        $penalite = new Penalite([
            'etudiant_id' => $donnees['etudiant_id'],
            'annee_acad_id' => $donnees['annee_acad_id'] ?? null,
            'montant' => $donnees['montant'],
            'motif' => $donnees['motif'] ?? 'Pénalité de retard',
            'date_application' => $donnees['date_application'] ?? date('Y-m-d'),
            'payee' => false,
            'type' => $donnees['type'] ?? 'Manuel',
            'creee_par' => $creePar,
        ]);
        $penalite->save();

        ServiceAudit::logCreation('penalite', $penalite->getId(), [
            'etudiant_id' => $donnees['etudiant_id'],
            'montant' => $donnees['montant'],
            'motif' => $penalite->motif,
        ]);

        // Notifier l'étudiant
        $this->notifierPenalite($penalite, $etudiant);

        return $penalite;
    }

    /**
     * Calcule les pénalités automatiques de retard
     */
    public function calculerPenalitesRetard(int $etudiantId, int $anneeAcadId): ?Penalite
    {
        // Récupérer le montant dû et payé
        $servicePaiement = new ServicePaiement();
        $solde = $servicePaiement->calculerSolde($etudiantId, $anneeAcadId);

        if ($solde['est_complet']) {
            return null; // Pas de retard
        }

        // Récupérer la date limite de paiement
        $dateLimite = $this->getDateLimitePaiement($anneeAcadId);
        if ($dateLimite === null) {
            return null;
        }

        // Calculer le nombre de jours de retard
        $joursGrace = $this->getJoursGrace();
        $dateAvecGrace = strtotime($dateLimite . " +{$joursGrace} days");
        $aujourdhui = time();

        if ($aujourdhui <= $dateAvecGrace) {
            return null; // Dans la période de grâce
        }

        $joursRetard = (int) ceil(($aujourdhui - $dateAvecGrace) / 86400);
        if ($joursRetard <= 0) {
            return null;
        }

        // Calculer le montant de la pénalité
        $tauxJour = $this->getTauxJour();
        $plafond = $this->getPlafond();
        $montantRestant = $solde['solde_restant'];

        $penaliteCalculee = $montantRestant * ($tauxJour / 100) * $joursRetard;
        $penaliteMax = $montantRestant * ($plafond / 100);

        $montantPenalite = min($penaliteCalculee, $penaliteMax);

        // Vérifier si une pénalité automatique existe déjà pour ce mois et cette année
        $moisActuel = date('Y-m');
        $sqlExistante = "SELECT * FROM penalites 
                         WHERE etudiant_id = :etudiant 
                         AND annee_acad_id = :annee 
                         AND type = 'Automatique'
                         AND DATE_FORMAT(date_application, '%Y-%m') = :mois
                         AND payee = 0 AND (annulee IS NULL OR annulee = 0)
                         LIMIT 1";
        $stmt = \App\Orm\Model::raw($sqlExistante, [
            'etudiant' => $etudiantId,
            'annee' => $anneeAcadId,
            'mois' => $moisActuel,
        ]);
        $penaliteExistanteData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($penaliteExistanteData !== false) {
            // Mettre à jour la pénalité existante du même mois
            $penaliteExistante = Penalite::find((int) $penaliteExistanteData['id_penalite']);
            if ($penaliteExistante !== null) {
                $penaliteExistante->montant = $montantPenalite;
                $penaliteExistante->motif = "Retard de paiement ({$joursRetard} jours)";
                $penaliteExistante->jours_retard = $joursRetard;
                $penaliteExistante->save();
                return $penaliteExistante;
            }
        }

        // Créer une nouvelle pénalité automatique
        $etudiant = Etudiant::find($etudiantId);
        $penalite = new Penalite([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
            'montant' => $montantPenalite,
            'motif' => "Retard de paiement ({$joursRetard} jours)",
            'date_application' => date('Y-m-d'),
            'payee' => false,
            'type' => 'Automatique',
            'jours_retard' => $joursRetard,
        ]);
        $penalite->save();

        ServiceAudit::logCreation('penalite_auto', $penalite->getId(), [
            'etudiant_id' => $etudiantId,
            'montant' => $montantPenalite,
            'jours_retard' => $joursRetard,
        ]);

        // Notifier l'étudiant
        if ($etudiant !== null) {
            $this->notifierPenalite($penalite, $etudiant);
        }

        return $penalite;
    }

    /**
     * Récupère la date limite de paiement pour une année
     */
    private function getDateLimitePaiement(int $anneeAcadId): ?string
    {
        $sql = "SELECT date_limite_paiement FROM frais_scolarite WHERE annee_acad_id = :annee";
        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['date_limite_paiement'] ?? null;
    }

    /**
     * Retourne le taux journalier configuré
     */
    private function getTauxJour(): float
    {
        return (float) ServiceParametres::get('finance.penalite.taux_jour', self::TAUX_JOUR_DEFAUT);
    }

    /**
     * Retourne le plafond configuré
     */
    private function getPlafond(): float
    {
        return (float) ServiceParametres::get('finance.penalite.plafond', self::PLAFOND_DEFAUT);
    }

    /**
     * Retourne les jours de grâce configurés
     */
    private function getJoursGrace(): int
    {
        return (int) ServiceParametres::get('finance.penalite.grace_jours', self::JOURS_GRACE_DEFAUT);
    }

    /**
     * Notifie l'étudiant d'une pénalité
     */
    private function notifierPenalite(Penalite $penalite, Etudiant $etudiant): void
    {
        if ($etudiant->utilisateur_id === null) {
            return;
        }

        ServiceNotification::envoyerParCode(
            'penalite_appliquee',
            [(int) $etudiant->utilisateur_id],
            [
                'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                'montant' => number_format((float) $penalite->montant, 0, ',', ' '),
                'motif' => $penalite->motif,
                'date' => date('d/m/Y'),
            ]
        );
    }

    /**
     * Enregistre le paiement d'une pénalité
     */
    public function payerPenalite(int $penaliteId, float $montant, int $payePar): Penalite
    {
        $penalite = Penalite::find($penaliteId);
        if ($penalite === null) {
            throw new NotFoundException('Pénalité non trouvée');
        }

        if ($penalite->payee) {
            throw new ValidationException('Cette pénalité est déjà payée');
        }

        if ($montant < (float) $penalite->montant) {
            throw new ValidationException('Le montant payé est insuffisant');
        }

        $penalite->payee = true;
        $penalite->date_paiement = date('Y-m-d H:i:s');
        $penalite->payee_par = $payePar;
        $penalite->save();

        ServiceAudit::log('paiement_penalite', 'penalite', $penaliteId, [
            'montant' => $montant,
        ]);

        // Générer le reçu de pénalité
        $this->genererRecuPenalite($penalite);

        return $penalite;
    }

    /**
     * Génère le reçu de pénalité
     */
    private function genererRecuPenalite(Penalite $penalite): void
    {
        $etudiant = Etudiant::find((int) $penalite->etudiant_id);
        if ($etudiant === null) {
            return;
        }

        $donnees = [
            'numero_recu' => 'PEN-' . $penalite->getId() . '-' . date('Ymd'),
            'date' => date('d/m/Y'),
            'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
            'motif' => $penalite->motif,
            'montant' => number_format((float) $penalite->montant, 0, ',', ' '),
        ];

        try {
            $resultat = ServicePdf::generer(
                ServicePdf::TYPE_RECU_PENALITE,
                $donnees,
                (int) ($penalite->payee_par ?? 0),
                'penalite',
                $penalite->getId()
            );

            $penalite->chemin_recu = $resultat['path'];
            $penalite->save();
        } catch (\Exception $e) {
            error_log('Erreur génération reçu pénalité: ' . $e->getMessage());
        }
    }

    /**
     * Retourne le total des pénalités d'un étudiant
     */
    public function getTotalPenalites(int $etudiantId, int $anneeAcadId): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total
                FROM penalites 
                WHERE etudiant_id = :etudiant 
                AND annee_acad_id = :annee 
                AND payee = 0";

        $stmt = Model::raw($sql, ['etudiant' => $etudiantId, 'annee' => $anneeAcadId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Retourne les pénalités d'un étudiant
     */
    public function getPenalites(int $etudiantId, ?int $anneeAcadId = null): array
    {
        $sql = "SELECT * FROM penalites WHERE etudiant_id = :etudiant";
        $params = ['etudiant' => $etudiantId];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $sql .= " ORDER BY date_application DESC";

        $stmt = Model::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Annule une pénalité
     */
    public function annuler(int $penaliteId, string $motif, int $annulePar): bool
    {
        $penalite = Penalite::find($penaliteId);
        if ($penalite === null) {
            throw new NotFoundException('Pénalité non trouvée');
        }

        if ($penalite->payee) {
            throw new ValidationException('Impossible d\'annuler une pénalité déjà payée');
        }

        $penalite->annulee = true;
        $penalite->motif_annulation = $motif;
        $penalite->annulee_par = $annulePar;
        $penalite->annulee_le = date('Y-m-d H:i:s');
        $penalite->save();

        ServiceAudit::log('annulation_penalite', 'penalite', $penaliteId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Exécute le calcul automatique des pénalités pour tous les étudiants
     */
    public function calculerToutesPenalites(int $anneeAcadId): array
    {
        $resultats = [
            'traites' => 0,
            'penalites_creees' => 0,
            'erreurs' => 0,
        ];

        // Récupérer tous les étudiants avec un solde non réglé
        $sql = "SELECT DISTINCT e.id_etudiant
                FROM etudiants e
                INNER JOIN dossiers_etudiants de ON de.etudiant_id = e.id_etudiant
                WHERE de.annee_acad_id = :annee
                AND e.actif = 1";

        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $etudiants = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($etudiants as $etudiantId) {
            $resultats['traites']++;
            try {
                $penalite = $this->calculerPenalitesRetard((int) $etudiantId, $anneeAcadId);
                if ($penalite !== null) {
                    $resultats['penalites_creees']++;
                }
            } catch (\Exception $e) {
                $resultats['erreurs']++;
                error_log("Erreur calcul pénalité étudiant {$etudiantId}: " . $e->getMessage());
            }
        }

        return $resultats;
    }

    /**
     * Retourne les statistiques des pénalités
     */
    public function getStatistiques(int $anneeAcadId): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_penalites,
                    SUM(montant) as montant_total,
                    SUM(CASE WHEN payee = 1 THEN montant ELSE 0 END) as montant_recouvre,
                    SUM(CASE WHEN payee = 0 AND annulee = 0 THEN montant ELSE 0 END) as montant_restant,
                    COUNT(CASE WHEN payee = 1 THEN 1 END) as penalites_payees,
                    COUNT(CASE WHEN payee = 0 AND annulee = 0 THEN 1 END) as penalites_en_cours,
                    COUNT(CASE WHEN annulee = 1 THEN 1 END) as penalites_annulees
                FROM penalites 
                WHERE annee_acad_id = :annee";

        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
