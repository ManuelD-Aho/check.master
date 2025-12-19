<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Semestre
 * 
 * Représente un semestre d'une année académique.
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

    // ===== RELATIONS =====

    /**
     * Retourne l'année académique
     */
    public function anneeAcademique(): ?AnneeAcademique
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_acad_id', 'id_annee_acad');
    }

    /**
     * Retourne les UE de ce semestre
     * @return Ue[]
     */
    public function ues(): array
    {
        return $this->hasMany(Ue::class, 'semestre_id', 'id_semestre');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les semestres d'une année académique
     * @return self[]
     */
    public static function pourAnnee(int $anneeAcadId): array
    {
        $sql = "SELECT * FROM semestre WHERE annee_acad_id = :id ORDER BY date_debut";
        $stmt = self::raw($sql, ['id' => $anneeAcadId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne le semestre actuel
     */
    public static function actuel(): ?self
    {
        $sql = "SELECT * FROM semestre 
                WHERE date_debut <= CURDATE() AND date_fin >= CURDATE()
                LIMIT 1";
        $stmt = self::raw($sql, []);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le semestre est en cours
     */
    public function estEnCours(): bool
    {
        $now = time();
        $debut = strtotime($this->date_debut);
        $fin = strtotime($this->date_fin);
        return $now >= $debut && $now <= $fin;
    }

    /**
     * Vérifie si le semestre est passé
     */
    public function estPasse(): bool
    {
        return strtotime($this->date_fin) < time();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne la durée en jours
     */
    public function getDureeJours(): int
    {
        $debut = new \DateTime($this->date_debut);
        $fin = new \DateTime($this->date_fin);
        return $debut->diff($fin)->days;
    }

    /**
     * Compte le total de crédits des UE du semestre
     */
    public function totalCredits(): int
    {
        $sql = "SELECT COALESCE(SUM(credits), 0) FROM ue WHERE semestre_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
