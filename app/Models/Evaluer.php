<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Evaluer
 * 
 * Association Enseignant <-> Entité évaluée (si différente de EvaluationRapport).
 * Table: evaluer
 */
class Evaluer extends Model
{
    protected string $table = 'evaluer';
    protected array $fillable = [
        'enseignant_id',
        'entite_id', // id rapport ou soutenance
        'entite_type',
        'note',
        'date_evaluation',
    ];
    // Clé composite virtuelle
}
