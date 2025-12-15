<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle DossierWorkflow
 * 
 * État courant du workflow pour un dossier.
 * Table: dossier_workflow
 */
class DossierWorkflow extends Model
{
    protected string $table = 'dossier_workflow';
    protected string $primaryKey = 'dossier_id';
    protected array $fillable = [
        'dossier_id',
        'etat_actuel', // code_etat
        'date_entree',
        'derniere_maj',
    ];

    /**
     * Retourne le dossier
     */
    public function getDossier(): ?DossierEtudiant
    {
        return DossierEtudiant::find((int) $this->dossier_id);
    }

    /**
     * Retourne l'état (objet)
     */
    public function getEtat(): ?WorkflowEtat
    {
        return WorkflowEtat::findByCode($this->etat_actuel);
    }
}
