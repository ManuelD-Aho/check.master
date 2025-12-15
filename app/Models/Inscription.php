<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Inscription
 * 
 * Inscription annuelle d'un étudiant.
 * Table: inscriptions
 */
class Inscription extends Model
{
    protected string $table = 'inscriptions';
    protected string $primaryKey = 'id_inscription';
    protected array $fillable = [
        'etudiant_id',
        'annee_acad_id',
        'niveau_etude_id',
        'filiere_id',
        'date_inscription',
        'statut', // 'Validee', 'En_attente'
        'montant_du',
        'montant_paye',
    ];

    /**
     * Retourne l'étudiant
     */
    public function getEtudiant(): ?Etudiant
    {
        return Etudiant::find((int) $this->etudiant_id);
    }

    /**
     * Retourne l'année
     */
    public function getAnnee(): ?AnneeAcademique
    {
        return AnneeAcademique::find((int) $this->annee_acad_id);
    }
}
