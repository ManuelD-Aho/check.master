<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle SessionParticipant
 * 
 * Participant à une session (réunion, commission, etc.).
 * Table: session_participants
 */
class SessionParticipant extends Model
{
    protected string $table = 'session_participants';
    protected array $fillable = [
        'session_id',
        'participant_id', // user_id
        'role', // 'invité', 'modérateur'
        'present', // bool
        'heure_arrivee',
    ];
}
