<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Deposer
 * 
 * Action de dépôt d'un document.
 * Table: deposer
 */
class Deposer extends Model
{
    protected string $table = 'deposer';
    protected string $primaryKey = 'id_depot';
    protected array $fillable = [
        'etudiant_id',
        'document_id',
        'date_depot',
        'commentaire',
    ];
}
