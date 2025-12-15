<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Traitement
 * 
 * Traitement batch ou tâche de fond.
 * Table: traitements
 */
class Traitement extends Model
{
    protected string $table = 'traitements';
    protected string $primaryKey = 'id_traitement';
    protected array $fillable = [
        'nom',
        'statut', // 'EN_COURS', 'TERMINE', 'ERREUR'
        'debut',
        'fin',
        'message_erreur',
    ];
}
