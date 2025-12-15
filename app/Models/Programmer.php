<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Programmer
 * 
 * Action de programmer une soutenance (logs ou planification).
 * Table: programmer
 */
class Programmer extends Model
{
    protected string $table = 'programmer';
    protected array $fillable = [
        'soutenance_id',
        'salle_id',
        'date_soutenance',
        'heure_debut',
    ];
}
