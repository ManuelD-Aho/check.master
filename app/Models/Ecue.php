<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ecue (Élément Constitutif d'UE)
 * 
 * Représente un élément constitutif d'une UE.
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

    /**
     * Trouve un ECUE par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ecue' => $code]);
    }

    /**
     * Retourne l'UE parente
     */
    public function getUe(): ?Ue
    {
        if ($this->ue_id === null) {
            return null;
        }
        return Ue::find((int) $this->ue_id);
    }

    /**
     * Retourne les ECUE d'une UE
     *
     * @return self[]
     */
    public static function parUe(int $ueId): array
    {
        return self::where(['ue_id' => $ueId]);
    }
}
