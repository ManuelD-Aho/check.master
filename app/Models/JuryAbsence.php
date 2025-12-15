<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle JuryAbsence
 * 
 * Absence justifiée ou non d'un membre du jury.
 * Table: jury_absences
 */
class JuryAbsence extends Model
{
    protected string $table = 'jury_absences';
    protected string $primaryKey = 'id_absence';
    protected array $fillable = [
        'jury_membre_id',
        'soutenance_id',
        'motif',
        'justifiee', // bool
        'remplace_par', // enseignant_id si remplacement
    ];
}
