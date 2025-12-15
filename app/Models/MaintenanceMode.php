<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle MaintenanceMode
 * 
 * Gestion du mode maintenance.
 * Table: maintenance_mode
 */
class MaintenanceMode extends Model
{
    protected string $table = 'maintenance_mode';
    protected string $primaryKey = 'id_maintenance';
    protected array $fillable = [
        'actif',
        'message',
        'autoriser_ips', // JSON list
        'date_debut',
        'date_fin_prevue',
    ];

    public static function estActif(): bool
    {
        $mode = self::firstWhere(['actif' => true]);
        return $mode !== null;
    }
}
