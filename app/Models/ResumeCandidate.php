<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle ResumeCandidate
 * 
 * Résumé de candidature soumis par l'étudiant.
 * Table: resumes_candidatures
 */
class ResumeCandidate extends Model
{
    protected string $table = 'resumes_candidatures';
    protected string $primaryKey = 'id_resume';
    protected array $fillable = [
        'candidature_id',
        'resume_texte',
        'mots_cles',
        'v_anglais', // Version anglaise ?
    ];
}
