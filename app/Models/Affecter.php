<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Affecter
 * 
 * Représente l'affectation d'un enseignant à une UE pour une année académique.
 * Table: affecter
 */
class Affecter extends Model
{
    protected string $table = 'affecter';
    protected string $primaryKey = 'id_affectation';
    protected array $fillable = [
        'enseignant_id',
        'ue_id',
        'annee_acad_id',
        'volume_horaire',
        'groupe_cm', // td, tp...
        'groupe_td',
        'groupe_tp',
    ];

    /**
     * Retourne l'enseignant
     */
    public function getEnseignant(): ?Enseignant
    {
        if ($this->enseignant_id === null) {
            return null;
        }
        return Enseignant::find((int) $this->enseignant_id);
    }

    /**
     * Retourne l'UE
     */
    public function getUe(): ?Ue
    {
        if ($this->ue_id === null) {
            return null;
        }
        return Ue::find((int) $this->ue_id);
    }

    /**
     * Retourne l'année académique
     */
    public function getAnneeAcademique(): ?AnneeAcademique
    {
        if ($this->annee_acad_id === null) {
            return null;
        }
        return AnneeAcademique::find((int) $this->annee_acad_id);
    }

    /**
     * Retourne les affectations d'un enseignant pour une année
     *
     * @return self[]
     */
    public static function pourEnseignant(int $enseignantId, int $anneeId): array
    {
        return self::where([
            'enseignant_id' => $enseignantId,
            'annee_acad_id' => $anneeId,
        ]);
    }
}
