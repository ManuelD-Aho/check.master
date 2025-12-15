<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauEtude
 * 
 * Représente un niveau d'étude (L1, L2, L3, M1, M2).
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

    /**
     * Trouve un niveau par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_niveau' => $libelle]);
    }

    /**
     * Retourne tous les niveaux triés
     *
     * @return self[]
     */
    public static function tousTriees(): array
    {
        $sql = "SELECT * FROM niveau_etude ORDER BY ordre_niveau ASC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les UE de ce niveau
     */
    public function getUes(): array
    {
        $sql = "SELECT * FROM ue WHERE niveau_id = :id ORDER BY code_ue";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
