<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle InformationStage
 * 
 * Informations complémentaires sur le stage (hors candidature).
 * Table: informations_stage
 */
class InformationStage extends Model
{
    protected string $table = 'informations_stage';
    protected string $primaryKey = 'id_info_stage';
    protected array $fillable = [
        'dossier_id',
        'ville',
        'pays',
        'indemnite_mensuelle',
        'tuteur_entreprise_nom',
        'tuteur_entreprise_fonction',
        'conditions_hebergement',
    ];
}
