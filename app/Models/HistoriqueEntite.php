<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle HistoriqueEntite
 * 
 * Audit des modifications sur les entités (Versioning DB).
 * Table: historique_entites
 */
class HistoriqueEntite extends Model
{
    protected string $table = 'historique_entites';
    protected string . $primaryKey = 'id_historique';
    protected array $fillable = [
        'entite_table',
        'entite_id',
        'action', // 'INSERT', 'UPDATE', 'DELETE'
        'valeurs_avant', // JSON
        'valeurs_apres', // JSON
        'modifie_par', // user_id
        'date_modif',
        'ip_adresse',
    ];

    /**
     * Enregistre une modification
     */
    public static function enregistrer(
        string $table,
        int $id,
        string $action,
        ?array $avant,
        ?array $apres,
        ?int $userId
    ): void {
        $hist = new self([
            'entite_table' => $table,
            'entite_id' => $id,
            'action' => $action,
            'valeurs_avant' => $avant ? json_encode($avant) : null,
            'valeurs_apres' => $apres ? json_encode($apres) : null,
            'modifie_par' => $userId,
            'date_modif' => date('Y-m-d H:i:s'),
            'ip_adresse' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        $hist->save();
    }
}
