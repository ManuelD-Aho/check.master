<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauApprobation
 * 
 * Étape dans un processus d'approbation.
 * Table: niveaux_approbation
 */
class NiveauApprobation extends Model
{
    protected string $table = 'niveaux_approbation';
    protected string $primaryKey = 'id_niveau_approbation';
    protected array $fillable = [
        'workflow_id',
        'ordre',
        'role_requis_id',
        'libelle_etape',
    ];
}
