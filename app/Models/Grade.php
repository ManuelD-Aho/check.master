<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Grade
 * 
 * Représente un grade académique (Professeur, Maître de conférences, etc.).
 * Table: grades
 */
class Grade extends Model
{
    protected string $table = 'grades';
    protected string $primaryKey = 'id_grade';
    protected array $fillable = [
        'lib_grade',
        'niveau_hierarchique',
        'actif',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les enseignants de ce grade
     * @return Enseignant[]
     */
    public function enseignants(): array
    {
        return $this->hasMany(Enseignant::class, 'grade_id', 'id_grade');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_grade' => $libelle]);
    }

    /**
     * Retourne tous les grades actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les grades ordonnés par niveau hiérarchique
     * @return self[]
     */
    public static function parNiveauHierarchique(): array
    {
        $sql = "SELECT * FROM grades WHERE actif = 1 ORDER BY niveau_hierarchique DESC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le grade est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Compte les enseignants de ce grade
     */
    public function nombreEnseignants(): int
    {
        return Enseignant::count(['grade_id' => $this->getId(), 'actif' => true]);
    }

    /**
     * Vérifie si ce grade est supérieur à un autre
     */
    public function estSuperieurA(Grade $autre): bool
    {
        return ($this->niveau_hierarchique ?? 0) > ($autre->niveau_hierarchique ?? 0);
    }

    /**
     * Active le grade
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le grade
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
