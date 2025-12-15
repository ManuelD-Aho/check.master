<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Versement
 * 
 * Versement partiel lié à un paiement global ou échéancier.
 * Table: versements
 */
class Versement extends Model
{
    protected string $table = 'versements';
    protected string $primaryKey = 'id_versement';
    protected array $fillable = [
        'paiement_id', // si lié à un paiement parent
        'montant',
        'date_versement',
        'reference_bancaire',
    ];
}
