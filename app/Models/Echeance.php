<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Echeance
 * 
 * Dates limites importantes (ex: dépôt dossier).
 * Table: echeances
 */
class Echeance extends Model
{
    protected string $table = 'echeances';
    protected string $primaryKey = 'id_echeance';
    protected array $fillable = [
        'libelle',
        'date_limite',
        'type_echeance',
        'annee_acad_id',
        'actif',
    ];

    public static function actives(int $anneeId): array
    {
        return self::where([
            'annee_acad_id' => $anneeId,
            'actif' => true,
        ]);
    }
}
