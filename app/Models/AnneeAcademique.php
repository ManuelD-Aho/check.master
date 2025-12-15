<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle AnneeAcademique
 * 
 * Représente une année académique (ex: 2024-2025).
 * Table: annee_academique
 */
class AnneeAcademique extends Model
{
    protected string $table = 'annee_academique';
    protected string $primaryKey = 'id_annee_acad';
    protected array $fillable = [
        'lib_annee_acad',
        'date_debut',
        'date_fin',
        'est_active',
    ];

    /**
     * Retourne l'année académique active
     */
    public static function getActive(): ?self
    {
        return self::firstWhere(['est_active' => true]);
    }

    /**
     * Trouve une année par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_annee_acad' => $libelle]);
    }

    /**
     * Vérifie si l'année est active
     */
    public function estActive(): bool
    {
        return (bool) $this->est_active;
    }

    /**
     * Active cette année (désactive les autres)
     */
    public function activer(): void
    {
        // Désactiver toutes les autres années
        $sql = "UPDATE annee_academique SET est_active = 0";
        self::raw($sql, []);

        // Activer celle-ci
        $this->est_active = true;
        $this->save();
    }

    /**
     * Retourne les semestres de cette année
     */
    public function getSemestres(): array
    {
        $sql = "SELECT * FROM semestre WHERE annee_acad_id = :id ORDER BY lib_semestre";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Vérifie si une date est dans cette année académique
     */
    public function contientDate(\DateTime $date): bool
    {
        $dateStr = $date->format('Y-m-d');
        return $dateStr >= $this->date_debut && $dateStr <= $this->date_fin;
    }

    /**
     * Retourne toutes les années triées par date décroissante
     *
     * @return self[]
     */
    public static function toutesTriees(): array
    {
        $sql = "SELECT * FROM annee_academique ORDER BY date_debut DESC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Compte les étudiants inscrits cette année
     */
    public function nombreEtudiants(): int
    {
        $sql = "SELECT COUNT(DISTINCT etudiant_id) FROM dossiers_etudiants WHERE annee_acad_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
