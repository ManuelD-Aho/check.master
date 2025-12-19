<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Note
 * 
 * Représente une note attribuée par un membre du jury.
 * Table: notes_jury
 */
class Note extends Model
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
     * Coefficients par défaut
     */
    public const COEF_CONTENU = 0.4;
    public const COEF_PRESENTATION = 0.3;
    public const COEF_TRAVAIL = 0.3;

    /**
     * Retourne la soutenance
     */
    public function getSoutenance(): ?Soutenance
    {
        if ($this->soutenance_id === null) {
            return null;
        }
        return Soutenance::find((int) $this->soutenance_id);
    }

    /**
     * Retourne le membre du jury
     */
    public function getJuryMembre(): ?JuryMembre
    {
        if ($this->membre_jury_id === null) {
            return null;
        }
        return JuryMembre::find((int) $this->membre_jury_id);
    }

    /**
     * Calcule la note finale (moyenne pondérée ou simple)
     */
    public function calculerNoteFinale(): float
    {
        // Selon le PRD, la note finale est souvent la moyenne ou calculée via pondération
        // Ici on fait une moyenne simple des composantes si disponibles
        $composantes = [];
        if ($this->note_fond !== null) $composantes[] = $this->note_fond;
        if ($this->note_forme !== null) $composantes[] = $this->note_forme;
        if ($this->note_soutenance !== null) $composantes[] = $this->note_soutenance;

        if (empty($composantes)) return 0.0;

        return round(array_sum($composantes) / count($composantes), 2);
    }

    /**
     * Sauvegarde avec calcul automatique de la note finale
     */
    public function save(): bool
    {
        $this->note_finale = $this->calculerNoteFinale();
        return parent::save();
    }

    /**
     * Retourne les notes d'une soutenance
     *
     * @return self[]
     */
    public static function pourSoutenance(int $soutenanceId): array
    {
        return self::where(['soutenance_id' => $soutenanceId]);
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

        $total = 0;
        foreach ($notes as $note) {
            $total += (float) $note->note_finale;
        }

        return round($total / count($notes), 2);
    }

    /**
     * Vérifie si tous les membres ont noté
     */
    public static function tousOntNote(int $soutenanceId, int $nombreMembres): bool
    {
        $notes = self::pourSoutenance($soutenanceId);
        return count($notes) >= $nombreMembres;
    }
}
