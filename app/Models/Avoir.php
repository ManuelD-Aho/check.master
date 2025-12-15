<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Avoir
 * 
 * Table d'association générique ou spécifique selon le contexte (ex: Etudiant - Ressource).
 * Table: avoir
 */
class Avoir extends Model
{
    protected string $table = 'avoir';
    protected string $primaryKey = 'id_avoir'; // Clé artificielle probable
    protected array $fillable = [
        'entite_source_id',
        'entite_cible_id',
        'type_relation',
        'date_debut',
        'date_fin',
    ];
}
