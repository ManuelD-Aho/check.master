<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationHistorique
 * Table: notifications_historique
 */
class NotificationHistorique extends Model
{
    protected string $table = 'notifications_historique';
    protected string $primaryKey = 'id_historique';

    protected array $fillable = [
        'template_code',
        'destinataire_id',
        'canal',
        'sujet',
        'statut',
        'erreur_message'
    ];

    public function destinataire(): Model
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id');
    }
}
