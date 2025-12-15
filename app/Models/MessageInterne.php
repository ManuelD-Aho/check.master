<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle MessageInterne
 * 
 * Alias ou spécialisation de Message pour usage interne système.
 * Table: messages_internes
 */
class MessageInterne extends Model
{
    protected string $table = 'messages_internes';
    protected string $primaryKey = 'id_msg_interne';
    protected array $fillable = [
        'titre',
        'contenu',
        'destinataire_groupe_id',
        'date_publication',
        'expire_le',
    ];
}
