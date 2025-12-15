<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Signature
 * 
 * Signature électronique d'un document.
 * Table: signatures
 */
class Signature extends Model
{
    protected string $table = 'signatures';
    protected string $primaryKey = 'id_signature';
    protected array $fillable = [
        'document_id',
        'signataire_id',
        'hash_signature', // SHA256/RSA
        'date_signature',
        'ip_adresse',
        'validite', // bool
    ];
}
