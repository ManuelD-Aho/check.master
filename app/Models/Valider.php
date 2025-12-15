<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Valider
 * 
 * Action de validation (générique).
 * Table: valider
 */
class Valider extends Model
{
    protected string $table = 'valider';
    protected array $fillable = [
        'utilisateur_id',
        'objet_type',
        'objet_id',
        'date_validation',
    ];
    // Clé composite
}
