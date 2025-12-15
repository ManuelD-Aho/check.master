<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EmailBounce
 * 
 * Trace des emails rejetés ou non délivrés.
 * Table: email_bounces
 */
class EmailBounce extends Model
{
    protected string $table = 'email_bounces';
    protected string $primaryKey = 'id_bounce';
    protected array $fillable = [
        'email',
        'sujet',
        'raison_echec',
        'date_envoi',
        'code_erreur',
    ];
}
