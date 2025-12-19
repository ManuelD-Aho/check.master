<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ecue (Élément Constitutif d'UE)
 * 
 * Représente un Élément Constitutif d'Unité d'Enseignement.
 * Table: ecue
 */
class Ecue extends Model
{
    protected string $table = 'ecue';
    protected string $primaryKey = 'id_ecue';
    protected array $fillable = [
        'code_ecue',
        'lib_ecue',
        'ue_id',
        'credits',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne l'UE parente
     */
    public function ue(): ?Ue
    {
        return $this->belongsTo(Ue::class, 'ue_id', 'id_ue');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ecue' => $code]);
    }

    /**
     * Retourne les ECUE d'une UE
     * @return self[]
     */
    public static function parUe(int $ueId): array
    {
        return self::where(['ue_id' => $ueId]);
    }

    /**
     * Recherche d'ECUE
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM ecue 
                WHERE code_ecue LIKE :terme OR lib_ecue LIKE :terme
                ORDER BY code_ecue
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne le libellé complet (code + libellé)
     */
    public function getLibelleComplet(): string
    {
        return $this->code_ecue . ' - ' . $this->lib_ecue;
    }
}
