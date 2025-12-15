<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EvaluationRapport
 * 
 * Détail de l'évaluation du rapport par critère.
 * Table: evaluations_rapports
 */
class EvaluationRapport extends Model
{
    protected string $table = 'evaluations_rapports';
    protected string $primaryKey = 'id_evaluation';
    protected array $fillable = [
        'rapport_id',
        'critere_id',
        'enseignant_id', // Evaluateur
        'note',
        'commentaire',
    ];

    /**
     * Retourne le critère
     */
    public function getCritere(): ?CritereEvaluation
    {
        if ($this->critere_id === null) {
            return null;
        }
        return CritereEvaluation::find((int) $this->critere_id);
    }
}
