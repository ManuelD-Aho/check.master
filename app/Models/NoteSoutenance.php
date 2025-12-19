<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NoteSoutenance
 * 
 * Représente les notes attribuées par les membres du jury lors d'une soutenance.
 * Table: notes_soutenance
 */
class NoteSoutenance extends Model
{
    protected string $table = 'notes_soutenance';
    protected string $primaryKey = 'id_note';
    protected array $fillable = [
        'soutenance_id',
        'membre_jury_id',
        'note_fond',
        'note_forme',
        'note_soutenance',
        'note_finale',
        'mention',
        'commentaire',
    ];

    /**
     * Coefficients par défaut pour le calcul de la note finale
     */
    public const COEF_FOND = 0.40;
    public const COEF_FORME = 0.30;
    public const COEF_SOUTENANCE = 0.30;

    /**
     * Note maximale
     */
    public const NOTE_MAX = 20.00;

    // ===== RELATIONS =====

    /**
     * Retourne la soutenance associée
     */
    public function soutenance(): ?Soutenance
    {
        return $this->belongsTo(Soutenance::class, 'soutenance_id', 'id_soutenance');
    }

    /**
     * Retourne le membre du jury
     */
    public function membreJury(): ?JuryMembre
    {
        return $this->belongsTo(JuryMembre::class, 'membre_jury_id', 'id_membre_jury');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne toutes les notes d'une soutenance
     * @return self[]
     */
    public static function pourSoutenance(int $soutenanceId): array
    {
        $sql = "SELECT * FROM notes_soutenance WHERE soutenance_id = :id ORDER BY id_note";
        $stmt = self::raw($sql, ['id' => $soutenanceId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne la note d'un membre pour une soutenance
     */
    public static function pourMembreSoutenance(int $soutenanceId, int $membreJuryId): ?self
    {
        return self::firstWhere([
            'soutenance_id' => $soutenanceId,
            'membre_jury_id' => $membreJuryId,
        ]);
    }

    /**
     * Retourne toutes les notes d'un membre du jury
     * @return self[]
     */
    public static function parMembre(int $membreJuryId): array
    {
        return self::where(['membre_jury_id' => $membreJuryId]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre une note
     */
    public static function enregistrer(
        int $soutenanceId,
        int $membreJuryId,
        ?float $noteFond = null,
        ?float $noteForme = null,
        ?float $noteSoutenance = null,
        ?string $commentaire = null
    ): self {
        // Vérifier si une note existe déjà
        $existante = self::pourMembreSoutenance($soutenanceId, $membreJuryId);

        if ($existante !== null) {
            // Mise à jour
            $existante->note_fond = $noteFond;
            $existante->note_forme = $noteForme;
            $existante->note_soutenance = $noteSoutenance;
            $existante->commentaire = $commentaire;
            $existante->calculerEtEnregistrerNoteFinale();
            $existante->save();
            return $existante;
        }

        // Nouvelle note
        $note = new self([
            'soutenance_id' => $soutenanceId,
            'membre_jury_id' => $membreJuryId,
            'note_fond' => $noteFond,
            'note_forme' => $noteForme,
            'note_soutenance' => $noteSoutenance,
            'commentaire' => $commentaire,
        ]);
        $note->calculerEtEnregistrerNoteFinale();
        $note->save();
        return $note;
    }

    /**
     * Calcule la note finale pondérée
     */
    public function calculerNoteFinale(): float
    {
        $total = 0.0;
        $coefTotal = 0.0;

        if ($this->note_fond !== null) {
            $total += (float) $this->note_fond * self::COEF_FOND;
            $coefTotal += self::COEF_FOND;
        }
        if ($this->note_forme !== null) {
            $total += (float) $this->note_forme * self::COEF_FORME;
            $coefTotal += self::COEF_FORME;
        }
        if ($this->note_soutenance !== null) {
            $total += (float) $this->note_soutenance * self::COEF_SOUTENANCE;
            $coefTotal += self::COEF_SOUTENANCE;
        }

        if ($coefTotal === 0.0) {
            return 0.0;
        }

        return round($total / $coefTotal, 2);
    }

    /**
     * Calcule et enregistre la note finale + mention
     */
    public function calculerEtEnregistrerNoteFinale(): void
    {
        $this->note_finale = $this->calculerNoteFinale();
        $this->mention = $this->determinerMention($this->note_finale);
    }

    /**
     * Détermine la mention à partir de la note
     */
    public function determinerMention(float $note): string
    {
        if ($note >= 16) {
            return 'Très Bien';
        } elseif ($note >= 14) {
            return 'Bien';
        } elseif ($note >= 12) {
            return 'Assez Bien';
        } elseif ($note >= 10) {
            return 'Passable';
        }
        return 'Insuffisant';
    }

    /**
     * Calcule la moyenne des notes d'une soutenance
     */
    public static function moyenneSoutenance(int $soutenanceId): ?float
    {
        $notes = self::pourSoutenance($soutenanceId);

        if (empty($notes)) {
            return null;
        }

        $total = 0.0;
        $count = 0;
        foreach ($notes as $note) {
            if ($note->note_finale !== null) {
                $total += (float) $note->note_finale;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return round($total / $count, 2);
    }

    /**
     * Vérifie si tous les membres ont noté
     */
    public static function tousOntNote(int $soutenanceId, int $nombreMembres): bool
    {
        $notes = self::pourSoutenance($soutenanceId);
        return count($notes) >= $nombreMembres;
    }

    /**
     * Compte le nombre de notes enregistrées pour une soutenance
     */
    public static function nombreNotes(int $soutenanceId): int
    {
        $sql = "SELECT COUNT(*) FROM notes_soutenance WHERE soutenance_id = :id";
        $stmt = self::raw($sql, ['id' => $soutenanceId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si la note est complète
     */
    public function estComplete(): bool
    {
        return $this->note_fond !== null
            && $this->note_forme !== null
            && $this->note_soutenance !== null;
    }

    /**
     * Vérifie si c'est un brouillon (note partielle)
     */
    public function estBrouillon(): bool
    {
        return !$this->estComplete();
    }

    /**
     * Retourne les statistiques de notation pour une soutenance
     */
    public static function statistiquesSoutenance(int $soutenanceId): array
    {
        $notes = self::pourSoutenance($soutenanceId);

        if (empty($notes)) {
            return [
                'nombre_notes' => 0,
                'moyenne' => null,
                'min' => null,
                'max' => null,
                'ecart_type' => null,
            ];
        }

        $notesFinales = array_filter(
            array_map(fn($n) => $n->note_finale, $notes),
            fn($v) => $v !== null
        );

        if (empty($notesFinales)) {
            return [
                'nombre_notes' => count($notes),
                'moyenne' => null,
                'min' => null,
                'max' => null,
                'ecart_type' => null,
            ];
        }

        $moyenne = array_sum($notesFinales) / count($notesFinales);

        // Calcul écart-type
        $variance = 0.0;
        foreach ($notesFinales as $note) {
            $variance += pow($note - $moyenne, 2);
        }
        $ecartType = sqrt($variance / count($notesFinales));

        return [
            'nombre_notes' => count($notesFinales),
            'moyenne' => round($moyenne, 2),
            'min' => min($notesFinales),
            'max' => max($notesFinales),
            'ecart_type' => round($ecartType, 2),
        ];
    }

    /**
     * Override save pour calculer la note finale avant sauvegarde
     */
    public function save(): bool
    {
        $this->calculerEtEnregistrerNoteFinale();
        return parent::save();
    }
}
