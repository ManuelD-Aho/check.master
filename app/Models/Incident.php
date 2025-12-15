<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Incident
 * 
 * Rapport d'incident technique ou fonctionnel.
 * Table: incidents
 */
class Incident extends Model
{
    protected string $table = 'incidents';
    protected string $primaryKey = 'id_incident';
    protected array $fillable = [
        'description',
        'niveau_criticite', // 'Faible', 'Moyen', 'Critique'
        'resolu',
        'date_resolution',
        'signale_par',
        'date_signalement',
    ];
}
