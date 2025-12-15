<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle StatutJury
 * 
 * Référentiel des statuts de membre de jury (Invité, Accepté...).
 * Table: statuts_jury
 */
class StatutJury extends Model
{
    protected string $table = 'statuts_jury';
    protected string $primaryKey = 'id_statut';
    protected array $fillable = [
        'code_statut',
        'lib_statut',
    ];

    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_statut' => $code]);
    }
}
