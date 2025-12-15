<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle JuryMembreExt
 * 
 * Membre du jury externe (non enseignant de l'établissement).
 * Table: jury_membres_ext
 */
class JuryMembreExt extends Model
{
    protected string $table = 'jury_membres_ext';
    protected string $primaryKey = 'id_membre_ext';
    protected array $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'organisme',
        'qualite', // Expert, Tuteur...
    ];
}
