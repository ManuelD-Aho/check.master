<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Occuper
 * 
 * Association Enseignant <-> Fonction ou Poste.
 * Table: occuper
 */
class Occuper extends Model
{
    protected string $table = 'occuper';
    protected array $fillable = [
        'enseignant_id',
        'fonction_id',
        'date_debut',
        'date_fin',
    ];
    // Clé composite
}
