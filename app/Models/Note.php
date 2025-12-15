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
        'jury_membre_id',
        'note_contenu',
        'note_presentation',
        'note_travail',
        'note_globale',
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
        if ($this->jury_membre_id === null) {
            return null;
        }
        return JuryMembre::find((int) $this->jury_membre_id);
    }

    /**
     * Calcule la note globale
     */
    public function calculerNoteGlobale(): float
    {
        return round(
            ($this->note_contenu * self::COEF_CONTENU) +
                ($this->note_presentation * self::COEF_PRESENTATION) +
                ($this->note_travail * self::COEF_TRAVAIL),
            2
        );
    }

    /**
     * Sauvegarde avec calcul automatique de la note globale
     */
    public function save(): bool
    {
        $this->note_globale = $this->calculerNoteGlobale();
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
            $total += (float) $note->note_globale;
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
