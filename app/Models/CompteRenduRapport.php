<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CompteRenduRapport
 * 
 * Lien entre un compte rendu et un rapport étudiant.
 * Table: compte_rendu_rapports
 */
class CompteRenduRapport extends Model
{
    protected string $table = 'compte_rendu_rapports';
    protected array $fillable = [
        'compte_rendu_id',
        'rapport_id',
    ];
    protected string $primaryKey = 'compte_rendu_id'; // Simulé
}
