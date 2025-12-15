<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Groupe
 * 
 * Représente un groupe d'utilisateurs avec niveau hiérarchique.
 * Table: groupes
 */
class Groupe extends Model
{
    protected string $table = 'groupes';
    protected string $primaryKey = 'id_groupe';
    protected array $fillable = [
        'nom_groupe',
        'description',
        'niveau_hierarchique',
        'actif',
    ];

    /**
     * Vérifie si le groupe est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne les groupes actifs
     */
    public static function getGroupesActifs(): array
    {
        return self::where(['actif' => 1]);
    }

    /**
     * Retourne les groupes triés par niveau hiérarchique
     */
    public static function getAllTriesParNiveau(): array
    {
        $sql = "SELECT * FROM groupes WHERE actif = 1 ORDER BY niveau_hierarchique ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            return $model;
        }, $rows);
    }
}
