<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Escalade
 * Table: escalades
 */
class Escalade extends Model
{
    protected string $table = 'escalades';
    protected string $primaryKey = 'id_escalade';

    protected array $fillable = [
        'dossier_id',
        'type_escalade',
        'niveau_escalade',
        'description',
        'statut',
        'cree_par',
        'assignee_a'
    ];

    public function dossier(): Model
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id');
    }

    public function createur(): Model
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }

    public function assigneA(): Model
    {
        return $this->belongsTo(Utilisateur::class, 'assignee_a');
    }

    public function actions(): array
    {
        return $this->hasMany(EscaladeAction::class, 'escalade_id');
    }
}
