<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Rendre
 * 
 * L'étudiant rend un travail (mémoire, rapport).
 * Table: rendre
 */
class Rendre extends Model
{
    protected string $table = 'rendre';
    protected array $fillable = [
        'etudiant_id',
        'travail_id', // sujet de stage ou devoir
        'date_rendu',
        'chemin_fichier',
    ];
}
