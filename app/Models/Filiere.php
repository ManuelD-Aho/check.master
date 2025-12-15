<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Filiere
 * 
 * Représente une filière d'étude (Informatique, Gestion, etc.).
 * Table: filieres
 */
class Filiere extends Model
{
    protected string $table = 'filieres';
    protected string $primaryKey = 'id_filiere';
    protected array $fillable = [
        'code_filiere',
        'lib_filiere',
        'description',
        'responsable_id', // Enseignant
    ];

    /**
     * Retourne le responsable de la filière
     */
    public function getResponsable(): ?Enseignant
    {
        if ($this->responsable_id === null) {
            return null;
        }
        return Enseignant::find((int) $this->responsable_id);
    }
}
