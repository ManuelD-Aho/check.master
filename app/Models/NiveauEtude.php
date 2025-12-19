<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauEtude
 * 
 * Représente un niveau d'étude (Licence 1, Master 2, etc.).
 * Table: niveau_etude
 */
class NiveauEtude extends Model
{
    protected string $table = 'niveau_etude';
    protected string $primaryKey = 'id_niveau';
    protected array $fillable = [
        'lib_niveau',
        'description',
        'ordre_niveau',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les UE de ce niveau
     * @return Ue[]
     */
    public function ues(): array
    {
        return $this->hasMany(Ue::class, 'niveau_id', 'id_niveau');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_niveau' => $libelle]);
    }

    /**
     * Retourne tous les niveaux ordonnés
     * @return self[]
     */
    public static function ordonnes(): array
    {
        $sql = "SELECT * FROM niveau_etude ORDER BY ordre_niveau";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Compte les UE de ce niveau
     */
    public function nombreUes(): int
    {
        return Ue::count(['niveau_id' => $this->getId()]);
    }

    /**
     * Total des crédits des UE du niveau
     */
    public function totalCredits(): int
    {
        $sql = "SELECT COALESCE(SUM(credits), 0) FROM ue WHERE niveau_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
