<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Reclamation
 * 
 * Représente une réclamation étudiante.
 * Table: reclamations
 */
class Reclamation extends Model
{
    protected string $table = 'reclamations';
    protected string $primaryKey = 'id_reclamation';
    protected array $fillable = [
        'etudiant_id',
        'type_reclamation',
        'sujet',
        'description',
        'entite_concernee_id',
        'statut',
        'resolution',
        'motif_rejet',
        'prise_en_charge_par',
        'prise_en_charge_le',
        'resolue_par',
        'resolue_le',
    ];

    /**
     * Retourne l'étudiant associé
     */
    public function etudiant(): ?Etudiant
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne l'utilisateur qui a pris en charge
     */
    public function priseEnChargePar(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'prise_en_charge_par', 'id_utilisateur');
    }

    /**
     * Retourne l'utilisateur qui a résolu
     */
    public function resoluePar(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'resolue_par', 'id_utilisateur');
    }
}
