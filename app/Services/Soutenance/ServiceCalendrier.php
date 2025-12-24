<?php

declare(strict_types=1);

namespace App\Services\Soutenance;

use App\Models\Soutenance;
use App\Models\Salle;
use App\Models\DossierEtudiant;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use App\Services\Workflow\ServiceWorkflow;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ConflictException;

/**
 * Service Calendrier
 * 
 * Planification des soutenances avec gestion des conflits.
 */
class ServiceCalendrier
{
    /**
     * Planifie une soutenance
     */
    public function planifier(
        int $dossierId,
        string $dateSoutenance,
        string $heureDebut,
        string $heureFin,
        int $salleId,
        int $planifiePar
    ): Soutenance {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        $salle = Salle::find($salleId);
        if ($salle === null) {
            throw new NotFoundException('Salle non trouvée');
        }

        // Vérifier les conflits de salle
        if ($this->salleOccupee($salleId, $dateSoutenance, $heureDebut, $heureFin)) {
            throw new ConflictException('La salle est déjà occupée à cette date et heure');
        }

        // Vérifier les conflits avec les membres du jury
        $conflitsJury = $this->verifierConflitsJury($dossierId, $dateSoutenance, $heureDebut, $heureFin);
        if (!empty($conflitsJury)) {
            throw new ConflictException(
                'Conflit avec le jury: ' . implode(', ', $conflitsJury)
            );
        }

        $soutenance = new Soutenance([
            'dossier_id' => $dossierId,
            'date_soutenance' => $dateSoutenance,
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin,
            'salle_id' => $salleId,
            'statut' => 'Planifiee',
            'planifiee_par' => $planifiePar,
        ]);
        $soutenance->save();

        ServiceAudit::logCreation('soutenance', $soutenance->getId(), [
            'dossier_id' => $dossierId,
            'date' => $dateSoutenance,
            'salle_id' => $salleId,
        ]);

        // Avancer le workflow
        $this->avancerWorkflow($dossierId, $planifiePar);

        // Notifier les participants
        $this->notifierParticipants($soutenance, $dossier);

        return $soutenance;
    }

    /**
     * Vérifie si une salle est occupée
     */
    public function salleOccupee(
        int $salleId,
        string $date,
        string $heureDebut,
        string $heureFin,
        ?int $excludeSoutenanceId = null
    ): bool {
        $sql = "SELECT COUNT(*) FROM soutenances 
                WHERE salle_id = :salle 
                AND date_soutenance = :date
                AND statut != 'Annulee'
                AND (
                    (heure_debut < :fin AND heure_fin > :debut)
                )";

        $params = [
            'salle' => $salleId,
            'date' => $date,
            'debut' => $heureDebut,
            'fin' => $heureFin,
        ];

        if ($excludeSoutenanceId !== null) {
            $sql .= " AND id_soutenance != :exclude";
            $params['exclude'] = $excludeSoutenanceId;
        }

        $stmt = \App\Orm\Model::raw($sql, $params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie les conflits avec les membres du jury
     */
    public function verifierConflitsJury(
        int $dossierId,
        string $date,
        string $heureDebut,
        string $heureFin
    ): array {
        $conflits = [];

        // Récupérer les membres du jury
        $sql = "SELECT jm.enseignant_id, e.nom_ens, e.prenom_ens
                FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :dossier AND jm.statut = 'Accepte'";

        $stmt = \App\Orm\Model::raw($sql, ['dossier' => $dossierId]);
        $membres = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($membres as $membre) {
            // Vérifier si le membre a une autre soutenance au même moment
            $sql2 = "SELECT COUNT(*) FROM soutenances s
                     INNER JOIN jury_membres jm ON jm.dossier_id = s.dossier_id
                     WHERE jm.enseignant_id = :ens
                     AND s.date_soutenance = :date
                     AND s.statut != 'Annulee'
                     AND s.dossier_id != :dossier
                     AND (s.heure_debut < :fin AND s.heure_fin > :debut)";

            $stmt2 = \App\Orm\Model::raw($sql2, [
                'ens' => $membre['enseignant_id'],
                'date' => $date,
                'debut' => $heureDebut,
                'fin' => $heureFin,
                'dossier' => $dossierId,
            ]);

            if ((int) $stmt2->fetchColumn() > 0) {
                $conflits[] = $membre['nom_ens'] . ' ' . $membre['prenom_ens'];
            }
        }

        return $conflits;
    }

    /**
     * Avance le workflow après planification
     */
    private function avancerWorkflow(int $dossierId, int $utilisateurId): void
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            return;
        }

        $etat = $dossier->getEtatActuel();
        if ($etat !== null && $etat->code_etat === 'JURY_EN_CONSTITUTION') {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    $dossierId,
                    'SOUTENANCE_PLANIFIEE',
                    $utilisateurId,
                    'Soutenance planifiée'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition calendrier: ' . $e->getMessage());
            }
        }
    }

    /**
     * Notifie les participants de la soutenance
     */
    private function notifierParticipants(Soutenance $soutenance, DossierEtudiant $dossier): void
    {
        $etudiant = $dossier->getEtudiant();
        $destinataires = [];

        if ($etudiant !== null && $etudiant->utilisateur_id !== null) {
            $destinataires[] = (int) $etudiant->utilisateur_id;
        }

        // Ajouter les membres du jury
        $sql = "SELECT e.utilisateur_id FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :dossier AND jm.statut = 'Accepte'
                AND e.utilisateur_id IS NOT NULL";

        $stmt = \App\Orm\Model::raw($sql, ['dossier' => $dossier->getId()]);
        $juryUsers = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $destinataires = array_merge($destinataires, $juryUsers);

        if (!empty($destinataires)) {
            ServiceNotification::envoyerParCode(
                'soutenance_planifiee',
                $destinataires,
                [
                    'date' => $soutenance->date_soutenance,
                    'heure' => $soutenance->heure_debut,
                ]
            );
        }
    }

    /**
     * Annule une soutenance
     */
    public function annuler(int $soutenanceId, string $motif, int $annulePar): bool
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        $soutenance->statut = 'Annulee';
        $soutenance->motif_annulation = $motif;
        $soutenance->save();

        ServiceAudit::log('annulation_soutenance', 'soutenance', $soutenanceId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Retourne les salles disponibles à une date/heure
     */
    public function getSallesDisponibles(string $date, string $heureDebut, string $heureFin): array
    {
        $sql = "SELECT * FROM salles WHERE actif = 1 
                AND id_salle NOT IN (
                    SELECT salle_id FROM soutenances
                    WHERE date_soutenance = :date
                    AND statut != 'Annulee'
                    AND (heure_debut < :fin AND heure_fin > :debut)
                )
                ORDER BY nom_salle";

        $stmt = \App\Orm\Model::raw($sql, [
            'date' => $date,
            'debut' => $heureDebut,
            'fin' => $heureFin,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le planning d'une journée
     */
    public function getPlanningJour(string $date): array
    {
        $sql = "SELECT s.*, sa.nom_salle, e.nom_etu, e.prenom_etu
                FROM soutenances s
                INNER JOIN salles sa ON sa.id_salle = s.salle_id
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE s.date_soutenance = :date
                AND s.statut != 'Annulee'
                ORDER BY s.heure_debut";

        $stmt = \App\Orm\Model::raw($sql, ['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
