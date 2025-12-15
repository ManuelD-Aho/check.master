<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationQueue
 * Table: notifications_queue
 */
class NotificationQueue extends Model
{
    protected string $table = 'notifications_queue';
    protected string $primaryKey = 'id_queue';

    protected array $fillable = [
        'template_id',
        'destinataire_id',
        'canal',
        'variables_json',
        'priorite',
        'statut',
        'tentatives',
        'erreur_message',
        'envoye_le'
    ];

    public function template(): Model
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function destinataire(): Model
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id');
    }
}
