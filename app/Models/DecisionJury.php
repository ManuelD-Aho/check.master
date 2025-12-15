<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle DecisionJury
 * 
 * Référentiel des décisions possibles du jury.
 * Table: decisions_jury
 */
class DecisionJury extends Model
{
    protected string $table = 'decisions_jury';
    protected string $primaryKey = 'id_decision';
    protected array $fillable = [
        'code_decision', // ADMIS, AJOURNE, REFUSE
        'lib_decision',
    ];

    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_decision' => $code]);
    }
}
