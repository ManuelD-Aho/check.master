<?php

declare(strict_types=1);

namespace App\Services\Soutenance;

use App\Models\NoteSoutenance;
use App\Models\Soutenance;
use App\Models\Mention;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;

/**
 * Service Notes
 * 
 * Gestion des notes de soutenance et calcul des mentions.
 */
class ServiceNotes
{
    /**
     * Seuils des mentions
     */
    private const SEUIL_PASSABLE = 10;
    private const SEUIL_ASSEZ_BIEN = 12;
    private const SEUIL_BIEN = 14;
    private const SEUIL_TRES_BIEN = 16;
    private const SEUIL_EXCELLENT = 18;

    /**
     * Enregistre une note de jury
     */
    public function enregistrerNote(
        int $soutenanceId,
        int $juryMembreId,
        float $note,
        ?string $appreciation = null,
        int $saisiePar = 0
    ): NoteSoutenance {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            throw new NotFoundException('Soutenance non trouvée');
        }

        // Valider la note
        if ($note < 0 || $note > 20) {
            throw new ValidationException('La note doit être comprise entre 0 et 20');
        }

        // Vérifier si une note existe déjà
        $noteExistante = NoteSoutenance::firstWhere([
            'soutenance_id' => $soutenanceId,
            'jury_membre_id' => $juryMembreId,
        ]);

        if ($noteExistante !== null) {
            // Mettre à jour la note existante
            $noteExistante->note = $note;
            $noteExistante->appreciation = $appreciation;
            $noteExistante->modifie_le = date('Y-m-d H:i:s');
            $noteExistante->save();

            ServiceAudit::log('modification_note', 'note_soutenance', $noteExistante->getId(), [
                'note' => $note,
            ]);

            return $noteExistante;
        }

        $noteSoutenance = new NoteSoutenance([
            'soutenance_id' => $soutenanceId,
            'jury_membre_id' => $juryMembreId,
            'note' => $note,
            'appreciation' => $appreciation,
            'saisie_par' => $saisiePar,
        ]);
        $noteSoutenance->save();

        ServiceAudit::logCreation('note_soutenance', $noteSoutenance->getId(), [
            'soutenance_id' => $soutenanceId,
            'note' => $note,
        ]);

        return $noteSoutenance;
    }

    /**
     * Calcule la note finale d'une soutenance
     */
    public function calculerNoteFinale(int $soutenanceId): ?float
    {
        $notes = $this->getNotes($soutenanceId);

        if (empty($notes)) {
            return null;
        }

        $total = 0;
        $coeffTotal = 0;

        foreach ($notes as $note) {
            $coeff = (float) ($note['coefficient'] ?? 1);
            $total += (float) $note['note'] * $coeff;
            $coeffTotal += $coeff;
        }

        if ($coeffTotal === 0.0) {
            return null;
        }

        return round($total / $coeffTotal, 2);
    }

    /**
     * Détermine la mention selon la note
     */
    public function determinerMention(float $note): string
    {
        if ($note >= self::SEUIL_EXCELLENT) {
            return 'Excellent';
        }
        if ($note >= self::SEUIL_TRES_BIEN) {
            return 'Très Bien';
        }
        if ($note >= self::SEUIL_BIEN) {
            return 'Bien';
        }
        if ($note >= self::SEUIL_ASSEZ_BIEN) {
            return 'Assez Bien';
        }
        if ($note >= self::SEUIL_PASSABLE) {
            return 'Passable';
        }
        return 'Ajourné';
    }

    /**
     * Finalise les notes d'une soutenance
     */
    public function finaliserNotes(int $soutenanceId, int $utilisateurId): array
    {
        $noteFinale = $this->calculerNoteFinale($soutenanceId);

        if ($noteFinale === null) {
            throw new ValidationException('Impossible de calculer la note finale');
        }

        $mention = $this->determinerMention($noteFinale);

        // Mettre à jour la soutenance
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance !== null) {
            $soutenance->note_finale = $noteFinale;
            $soutenance->mention = $mention;
            $soutenance->notes_finalisees = true;
            $soutenance->finalisee_le = date('Y-m-d H:i:s');
            $soutenance->finalisee_par = $utilisateurId;
            $soutenance->save();
        }

        ServiceAudit::log('finalisation_notes', 'soutenance', $soutenanceId, [
            'note_finale' => $noteFinale,
            'mention' => $mention,
        ]);

        return [
            'note_finale' => $noteFinale,
            'mention' => $mention,
        ];
    }

    /**
     * Retourne les notes d'une soutenance
     */
    public function getNotes(int $soutenanceId): array
    {
        $sql = "SELECT ns.*, jm.role, e.nom_ens, e.prenom_ens
                FROM notes_soutenances ns
                INNER JOIN jury_membres jm ON jm.id_jury_membre = ns.jury_membre_id
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE ns.soutenance_id = :id";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $soutenanceId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si toutes les notes ont été saisies
     */
    public function toutesNotesSaisies(int $soutenanceId): bool
    {
        $soutenance = Soutenance::find($soutenanceId);
        if ($soutenance === null) {
            return false;
        }

        // Compter les membres du jury
        $sql = "SELECT COUNT(*) FROM jury_membres 
                WHERE dossier_id = :dossier AND statut = 'Accepte'";
        $stmt = \App\Orm\Model::raw($sql, ['dossier' => $soutenance->dossier_id]);
        $nombreJury = (int) $stmt->fetchColumn();

        // Compter les notes saisies
        $nombreNotes = NoteSoutenance::count(['soutenance_id' => $soutenanceId]);

        return $nombreNotes >= $nombreJury;
    }
}
