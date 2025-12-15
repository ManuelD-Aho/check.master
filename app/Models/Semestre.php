<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Semestre
 * 
 * Représente un semestre académique.
 * Table: semestre
 */
class Semestre extends Model
{
    protected string $table = 'semestre';
    protected string $primaryKey = 'id_semestre';
    protected array $fillable = [
        'lib_semestre',
        'annee_acad_id',
        'date_debut',
        'date_fin',
    ];

    /**
     * Retourne l'année académique de ce semestre
     */
    public function getAnneeAcademique(): ?AnneeAcademique
    {
        if ($this->annee_acad_id === null) {
            return null;
        }
        return AnneeAcademique::find((int) $this->annee_acad_id);
    }

    /**
     * Retourne les UE de ce semestre
     */
    public function getUes(): array
    {
        $sql = "SELECT * FROM ue WHERE semestre_id = :id ORDER BY code_ue";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Vérifie si le semestre est en cours
     */
    public function estEnCours(): bool
    {
        $now = date('Y-m-d');
        return $now >= $this->date_debut && $now <= $this->date_fin;
    }

    /**
     * Retourne les semestres d'une année académique
     *
     * @return self[]
     */
    public static function pourAnnee(int $anneeAcadId): array
    {
        return self::where(['annee_acad_id' => $anneeAcadId]);
    }
}
