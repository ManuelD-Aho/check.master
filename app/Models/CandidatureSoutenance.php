<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CandidatureSoutenance
 * 
 * Lien entre une candidature validée et une planification de soutenance.
 * Table: candidature_soutenance
 */
class CandidatureSoutenance extends Model
{
    protected string $table = 'candidature_soutenance';
    protected array $fillable = [
        'candidature_id',
        'soutenance_id',
    ];
    // Table pivot sans clé primaire unique par défaut, ou clé composite
    protected string $primaryKey = 'candidature_id'; // Simulé pour ORM simple
}
