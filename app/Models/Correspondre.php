<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Correspondre
 * 
 * Lien Utilisateur <-> Conversation (Participants).
 * Table: correspondre
 */
class Correspondre extends Model
{
    protected string $table = 'correspondre';
    protected array $fillable = [
        'conversation_id',
        'utilisateur_id',
        'vu_a',
        'est_admin',
    ];
    protected string $primaryKey = 'conversation_id'; // Composée en théorie
}
