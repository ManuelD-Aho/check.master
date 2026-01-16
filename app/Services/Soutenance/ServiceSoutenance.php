<?php

declare(strict_types=1);

namespace App\Services\Soutenance;

use App\Models\Soutenance;
use App\Models\DossierEtudiant;
use App\Models\NoteSoutenance;
use App\Services\Security\ServiceAudit;
use App\Services\Workflow\ServiceWorkflow;
use App\Services\Communication\ServiceNotification;
use App\Services\Documents\ServicePdf;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;
use App\Orm\Model;

/**
 * Service Soutenance
 * 
 * Gestion complète des soutenances: création, déroulement, finalisation.
 * Génération du PV de soutenance.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class ServiceSoutenance
{
    private ServiceJury $serviceJury;
    private ServiceNotes $serviceNotes;
    private ServiceCalendrier $serviceCalendrier;

    public function __construct()
    {
        $this->serviceJury = new ServiceJury();
        $this->serviceNotes = new ServiceNotes();
        $this->serviceCalendrier = new ServiceCalendrier();
    }

    /**
     * Démarre une soutenance (jour J)
     */
    public function demarrer(int $soutenanceId, int $demarrePar): Soutenance
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        if ($soutenance->statut !== 'Planifiee') {
            throw new ValidationException('Cette soutenance ne peut pas être démarrée');
        }

        // Vérifier que c'est le jour J
        $aujourdhui = date('Y-m-d');
        if ($soutenance->date_soutenance !== $aujourdhui) {
            throw new ValidationException('La soutenance ne peut être démarrée que le jour prévu');
        }

        // Vérifier que le jury est complet
        if (!$this->serviceJury->estComplet((int) $soutenance->dossier_id)) {
            throw new ValidationException('Le jury n\'est pas complet');
        }

        $soutenance->statut = 'En_cours';
        $soutenance->demarree_le = date('Y-m-d H:i:s');
        $soutenance->demarree_par = $demarrePar;
        $soutenance->save();

        // Avancer le workflow
        $serviceWorkflow = new ServiceWorkflow();
        try {
            $serviceWorkflow->effectuerTransition(
                (int) $soutenance->dossier_id,
                'SOUTENANCE_EN_COURS',
                $demarrePar,
                'Soutenance démarrée'
            );
        } catch (\Exception $e) {
            error_log('Erreur transition soutenance: ' . $e->getMessage());
        }

        ServiceAudit::log('demarrage_soutenance', 'soutenance', $soutenanceId);

        return $soutenance;
    }

    /**
     * Termine une soutenance avec les notes finalisées
     */
    public function terminer(int $soutenanceId, int $terminePar): array
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        if ($soutenance->statut !== 'En_cours') {
            throw new ValidationException('Cette soutenance n\'est pas en cours');
        }

        // Vérifier que toutes les notes ont été saisies
        if (!$this->serviceNotes->toutesNotesSaisies($soutenanceId)) {
            throw new ValidationException('Toutes les notes n\'ont pas été saisies');
        }

        // Finaliser les notes
        $resultats = $this->serviceNotes->finaliserNotes($soutenanceId, $terminePar);

        // Mettre à jour la soutenance
        $soutenance->statut = 'Terminee';
        $soutenance->terminee_le = date('Y-m-d H:i:s');
        $soutenance->terminee_par = $terminePar;
        $soutenance->note_finale = $resultats['note_finale'];
        $soutenance->mention = $resultats['mention'];
        $soutenance->save();

        // Avancer le workflow
        $serviceWorkflow = new ServiceWorkflow();
        try {
            $serviceWorkflow->effectuerTransition(
                (int) $soutenance->dossier_id,
                'SOUTENANCE_TERMINEE',
                $terminePar,
                'Soutenance terminée - ' . $resultats['mention']
            );
        } catch (\Exception $e) {
            error_log('Erreur transition fin soutenance: ' . $e->getMessage());
        }

        ServiceAudit::log('fin_soutenance', 'soutenance', $soutenanceId, [
            'note_finale' => $resultats['note_finale'],
            'mention' => $resultats['mention'],
        ]);

        // Générer le PV de soutenance
        $pv = $this->genererPV($soutenanceId, $terminePar);

        // Notifier l'étudiant des résultats
        $this->notifierResultats($soutenance, $resultats);

        return [
            'soutenance_id' => $soutenanceId,
            'note_finale' => $resultats['note_finale'],
            'mention' => $resultats['mention'],
            'pv' => $pv,
        ];
    }

    /**
     * Génère le PV de soutenance
     */
    public function genererPV(int $soutenanceId, int $generePar): array
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        $dossier = DossierEtudiant::find((int) $soutenance->dossier_id);
        $etudiant = $dossier?->getEtudiant();
        $candidature = $dossier?->getCandidature();

        $membres = $this->serviceJury->getMembres((int) $soutenance->dossier_id);
        $notes = $this->serviceNotes->getNotes($soutenanceId);

        // Préparer les données du PV
        $juryFormate = array_map(function ($m) {
            return $m['nom_ens'] . ' ' . $m['prenom_ens'] . ' (' . $m['role'] . ')';
        }, $membres);

        $donnees = [
            'date' => date('d/m/Y', strtotime($soutenance->date_soutenance ?? 'now')),
            'etudiant_nom' => $etudiant ? $etudiant->nom_etu . ' ' . $etudiant->prenom_etu : '',
            'sujet' => $candidature?->theme ?? '',
            'jury' => implode('<br>', $juryFormate),
            'note' => number_format((float) ($soutenance->note_finale ?? 0), 2),
            'mention' => $soutenance->mention ?? '',
            'decision' => ((float) ($soutenance->note_finale ?? 0) >= 10) ? 'Admis' : 'Ajourné',
        ];

        $resultat = ServicePdf::genererAvance(
            ServicePdf::TYPE_PV_SOUTENANCE,
            $donnees,
            $generePar,
            'soutenance',
            $soutenanceId
        );

        $soutenance->pv_genere = true;
        $soutenance->chemin_pv = $resultat['path'];
        $soutenance->save();

        return $resultat;
    }

    /**
     * Notifie l'étudiant des résultats
     */
    private function notifierResultats(Soutenance $soutenance, array $resultats): void
    {
        $dossier = DossierEtudiant::find((int) $soutenance->dossier_id);
        $etudiant = $dossier?->getEtudiant();

        if ($etudiant === null || $etudiant->utilisateur_id === null) {
            return;
        }

        $decision = ((float) $resultats['note_finale'] >= 10) ? 'Admis' : 'Ajourné';

        ServiceNotification::envoyerParCode(
            'resultats_soutenance',
            [(int) $etudiant->utilisateur_id],
            [
                'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                'note' => number_format($resultats['note_finale'], 2),
                'mention' => $resultats['mention'],
                'decision' => $decision,
            ]
        );
    }

    /**
     * Reporte une soutenance
     */
    public function reporter(
        int $soutenanceId,
        string $nouvelleDate,
        string $motif,
        int $reportePar
    ): Soutenance {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        if (!in_array($soutenance->statut, ['Planifiee', 'En_cours'], true)) {
            throw new ValidationException('Cette soutenance ne peut pas être reportée');
        }

        $ancienneDate = $soutenance->date_soutenance;
        $soutenance->date_soutenance = $nouvelleDate;
        $soutenance->statut = 'Reportee';
        $soutenance->motif_report = $motif;
        $soutenance->reportee_par = $reportePar;
        $soutenance->reportee_le = date('Y-m-d H:i:s');
        $soutenance->save();

        ServiceAudit::log('report_soutenance', 'soutenance', $soutenanceId, [
            'ancienne_date' => $ancienneDate,
            'nouvelle_date' => $nouvelleDate,
            'motif' => $motif,
        ]);

        // Notifier tous les participants
        $this->notifierReport($soutenance, $ancienneDate, $nouvelleDate, $motif);

        return $soutenance;
    }

    /**
     * Notifie le report d'une soutenance
     */
    private function notifierReport(Soutenance $soutenance, string $ancienne, string $nouvelle, string $motif): void
    {
        $dossier = DossierEtudiant::find((int) $soutenance->dossier_id);
        if ($dossier === null) {
            return;
        }

        $destinataires = [];

        // L'étudiant
        $etudiant = $dossier->getEtudiant();
        if ($etudiant !== null && $etudiant->utilisateur_id !== null) {
            $destinataires[] = (int) $etudiant->utilisateur_id;
        }

        // Les membres du jury
        $sql = "SELECT e.utilisateur_id FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :dossier AND jm.statut = 'Accepte'
                AND e.utilisateur_id IS NOT NULL";

        $stmt = Model::raw($sql, ['dossier' => $dossier->getId()]);
        $juryUsers = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $destinataires = array_merge($destinataires, $juryUsers);

        if (!empty($destinataires)) {
            ServiceNotification::envoyerParCode(
                'soutenance_reportee',
                $destinataires,
                [
                    'ancienne_date' => date('d/m/Y', strtotime($ancienne)),
                    'nouvelle_date' => date('d/m/Y', strtotime($nouvelle)),
                    'motif' => $motif,
                ]
            );
        }
    }

    /**
     * Enregistre les corrections demandées
     */
    public function demanderCorrections(
        int $soutenanceId,
        string $corrections,
        int $delaiJours,
        int $demandePar
    ): bool {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        $soutenance->corrections_demandees = true;
        $soutenance->corrections_liste = $corrections;
        $soutenance->corrections_delai = date('Y-m-d', strtotime("+{$delaiJours} days"));
        $soutenance->corrections_demandees_par = $demandePar;
        $soutenance->save();

        ServiceAudit::log('demande_corrections', 'soutenance', $soutenanceId, [
            'delai' => $delaiJours,
        ]);

        // Notifier l'étudiant
        $dossier = DossierEtudiant::find((int) $soutenance->dossier_id);
        $etudiant = $dossier?->getEtudiant();

        if ($etudiant !== null && $etudiant->utilisateur_id !== null) {
            ServiceNotification::envoyerParCode(
                'corrections_demandees',
                [(int) $etudiant->utilisateur_id],
                [
                    'corrections' => $corrections,
                    'delai' => date('d/m/Y', strtotime("+{$delaiJours} days")),
                ]
            );
        }

        return true;
    }

    /**
     * Valide les corrections post-soutenance
     */
    public function validerCorrections(int $soutenanceId, int $validePar): bool
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        if (!$soutenance->corrections_demandees) {
            throw new ValidationException('Aucune correction n\'a été demandée');
        }

        $soutenance->corrections_validees = true;
        $soutenance->corrections_validees_le = date('Y-m-d H:i:s');
        $soutenance->corrections_validees_par = $validePar;
        $soutenance->save();

        // Avancer au diplôme délivré
        $serviceWorkflow = new ServiceWorkflow();
        try {
            $serviceWorkflow->effectuerTransition(
                (int) $soutenance->dossier_id,
                'DIPLOME_DELIVRE',
                $validePar,
                'Corrections validées'
            );
        } catch (\Exception $e) {
            error_log('Erreur transition diplôme: ' . $e->getMessage());
        }

        ServiceAudit::log('validation_corrections', 'soutenance', $soutenanceId);

        return true;
    }

    /**
     * Retourne une soutenance avec tous ses détails
     */
    public function getDetails(int $soutenanceId): array
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        $dossier = DossierEtudiant::find((int) $soutenance->dossier_id);
        $etudiant = $dossier?->getEtudiant();
        $candidature = $dossier?->getCandidature();
        $membres = $this->serviceJury->getMembres((int) $soutenance->dossier_id);
        $notes = $this->serviceNotes->getNotes($soutenanceId);

        // Récupérer la salle
        $sql = "SELECT * FROM salles WHERE id_salle = :id";
        $stmt = Model::raw($sql, ['id' => $soutenance->salle_id]);
        $salle = $stmt->fetch(\PDO::FETCH_ASSOC);

        return [
            'soutenance' => $soutenance->toArray(),
            'etudiant' => $etudiant?->toArray(),
            'theme' => $candidature?->theme,
            'salle' => $salle,
            'jury' => $membres,
            'notes' => $notes,
            'jury_complet' => $this->serviceJury->estComplet((int) $soutenance->dossier_id),
        ];
    }

    /**
     * Retourne les soutenances du jour
     */
    public function getSoutenancesDuJour(): array
    {
        return $this->serviceCalendrier->getPlanningJour(date('Y-m-d'));
    }

    /**
     * Retourne les soutenances à venir
     */
    public function getSoutenancesAVenir(int $jours = 30): array
    {
        $dateDebut = date('Y-m-d');
        $dateFin = date('Y-m-d', strtotime("+{$jours} days"));

        $sql = "SELECT s.*, sa.nom_salle, e.nom_etu, e.prenom_etu, c.theme
                FROM soutenances s
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN candidatures c ON c.dossier_id = de.id_dossier
                LEFT JOIN salles sa ON sa.id_salle = s.salle_id
                WHERE s.date_soutenance BETWEEN :debut AND :fin
                AND s.statut IN ('Planifiee', 'Reportee')
                ORDER BY s.date_soutenance, s.heure_debut";

        $stmt = Model::raw($sql, ['debut' => $dateDebut, 'fin' => $dateFin]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les statistiques des soutenances
     */
    public function getStatistiques(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'Planifiee' THEN 1 ELSE 0 END) as planifiees,
                    SUM(CASE WHEN statut = 'En_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'Terminee' THEN 1 ELSE 0 END) as terminees,
                    SUM(CASE WHEN statut = 'Reportee' THEN 1 ELSE 0 END) as reportees,
                    SUM(CASE WHEN statut = 'Annulee' THEN 1 ELSE 0 END) as annulees,
                    AVG(CASE WHEN statut = 'Terminee' THEN note_finale ELSE NULL END) as moyenne_notes
                FROM soutenances";

        $stmt = Model::raw($sql);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Répartition par mention
        $sql2 = "SELECT mention, COUNT(*) as nombre
                 FROM soutenances 
                 WHERE statut = 'Terminee' AND mention IS NOT NULL
                 GROUP BY mention";

        $stmt2 = Model::raw($sql2);
        $parMention = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        return array_merge($stats ?: [], ['par_mention' => $parMention]);
    }
}
