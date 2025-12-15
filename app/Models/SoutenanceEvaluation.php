<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle SoutenanceEvaluation
 * 
 * Évaluation orale de la soutenance (grille).
 * Table: soutenance_evaluations
 */
class SoutenanceEvaluation extends Model
{
    protected string $table = 'soutenance_evaluations';
    protected string $primaryKey = 'id_eval_soutenance';
    protected array $fillable = [
        'soutenance_id',
        'critere_id',
        'jury_membre_id',
        'note',
        'commentaire',
    ];
}
