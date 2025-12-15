<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Composer_jury
 * 
 * Association Enseignant <-> Soutenance (Jury).
 * Préférez utiliser le modèle JuryMembre qui est plus complet.
 * Table: composer_jury
 */
class Composer_jury extends Model
{
    protected string $table = 'composer_jury';
    protected array $fillable = [
        'enseignant_id',
        'soutenance_id',
        'role',
    ];
    protected string $primaryKey = 'id'; // Placeholder
}
