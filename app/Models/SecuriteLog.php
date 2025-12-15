<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle SecuriteLog
 * 
 * Log des événements de sécurité (auth, firewall).
 * Table: securite_logs
 */
class SecuriteLog extends Model
{
    protected string $table = 'securite_logs';
    protected string $primaryKey = 'id_log';
    protected array $fillable = [
        'evenement', // 'LOGIN_FAIL', 'BLOCKED_IP'
        'details',
        'ip_adresse',
        'user_agent',
        'utilisateur_id',
        'date_log',
    ];
}
