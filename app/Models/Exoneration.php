<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Exoneration
 * 
 * Exonération de droits d'inscription ou autre.
 * Table: exonerations
 */
class Exoneration extends Model
{
    protected string $table = 'exonerations';
    protected string $primaryKey = 'id_exoneration';
    protected array $fillable = [
        'etudiant_id',
        'annee_acad_id',
        'montant_exonere',
        'motif',
        'date_decision',
        'approuve_par',
    ];
}
