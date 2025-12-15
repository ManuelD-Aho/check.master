<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Grade
 * 
 * Représente un grade académique (Professeur, MCF, etc.).
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

    /**
     * Trouve un grade par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_grade' => $libelle]);
    }

    /**
     * Retourne tous les grades actifs triés par niveau
     *
     * @return self[]
     */
    public static function actifs(): array
    {
        $sql = "SELECT * FROM grades WHERE actif = 1 ORDER BY niveau_hierarchique ASC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les enseignants avec ce grade
     */
    public function getEnseignants(): array
    {
        return Enseignant::where(['grade_id' => $this->getId(), 'actif' => true]);
    }
}
