<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EscaladeAction
 * Table: escalades_actions
 */
class EscaladeAction extends Model
{
    protected string $table = 'escalades_actions';
    protected string $primaryKey = 'id_action';

    protected array $fillable = [
        'escalade_id',
        'utilisateur_id',
        'type_action',
        'description'
    ];

    public function escalade(): Model
    {
        return $this->belongsTo(Escalade::class, 'escalade_id');
    }

    public function utilisateur(): Model
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
