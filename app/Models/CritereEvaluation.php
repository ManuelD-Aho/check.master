<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CritereEvaluation
 * 
 * Critère d'évaluation pour les rapports/soutenances.
 * Table: criteres_evaluation
 */
class CritereEvaluation extends Model
{
    protected string $table = 'criteres_evaluation';
    protected string $primaryKey = 'id_critere';
    protected array $fillable = [
        'libelle',
        'description',
        'ponderation',
        'type', // 'Rapport', 'Soutenance'
        'actif',
    ];

    public static function actifs(string $type): array
    {
        return self::where(['type' => $type, 'actif' => true]);
    }
}
