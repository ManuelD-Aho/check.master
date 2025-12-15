<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EvenementCalendrier
 * 
 * Événement affiché dans le calendrier (Soutenances, Congés, etc.).
 * Table: evenements_calendrier
 */
class EvenementCalendrier extends Model
{
    protected string $table = 'evenements_calendrier';
    protected string $primaryKey = 'id_evenement';
    protected array $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'type', // 'Soutenance', 'Reunion', 'Conge'
        'couleur',
        'cree_par',
    ];
}
