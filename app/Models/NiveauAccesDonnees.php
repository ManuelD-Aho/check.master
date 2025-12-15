<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauAccesDonnees
 * 
 * Niveau de confidentialité ou d'accès aux données.
 * Table: niveaux_acces_donnees
 */
class NiveauAccesDonnees extends Model
{
    protected string $table = 'niveaux_acces_donnees';
    protected string $primaryKey = 'id_niveau';
    protected array $fillable = [
        'libelle', // Public, Interne, Confidentiel, Secret
        'niveau_hierarchique', // int
    ];
}
