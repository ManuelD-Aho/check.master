<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CompteRendu
 * 
 * Compte rendu de réunion ou d'activité.
 * Table: comptes_rendus
 */
class CompteRendu extends Model
{
    protected string $table = 'comptes_rendus';
    protected string $primaryKey = 'id_compte_rendu';
    protected array $fillable = [
        'titre',
        'contenu',
        'date_redaction',
        'auteur_id',
        'type', // Reunion, Activite, Autre
        'pieces_jointes_json',
    ];

    /**
     * Retourne l'auteur
     */
    public function getAuteur(): ?Utilisateur
    {
        if ($this->auteur_id === null) {
            return null;
        }
        return Utilisateur::find((int) $this->auteur_id);
    }
}
