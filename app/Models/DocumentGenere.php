<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle DocumentGenere
 * 
 * Historique des documents générés par le système (PDFs).
 * Table: documents_generes
 */
class DocumentGenere extends Model
{
    protected string $table = 'documents_generes';
    protected string $primaryKey = 'id_doc_gen';
    protected array $fillable = [
        'type_document', // 'Attestation', 'Bordereau', 'PV'
        'reference_objet',
        'chemin_fichier',
        'genere_par', // user_id
        'date_generation',
        'hash_contenu',
    ];
}
