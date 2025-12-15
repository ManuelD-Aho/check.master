<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Message
 * 
 * Message dans une conversation.
 * Table: messages
 */
class Message extends Model
{
    protected string $table = 'messages';
    protected string $primaryKey = 'id_message';
    protected array $fillable = [
        'conversation_id',
        'sender_id',
        'contenu',
        'type_contenu', // 'text', 'image', 'file'
        'pieces_jointes', // JSON
        'envoye_a',
        'lu_par_tous',
    ];

    public function getExpediteur(): ?Utilisateur
    {
        return Utilisateur::find((int) $this->sender_id);
    }
}
