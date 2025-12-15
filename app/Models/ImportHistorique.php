<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle ImportHistorique
 * 
 * Historique des imports de données (CSV/Excel).
 * Table: imports_historique
 */
class ImportHistorique extends Model
{
    protected string $table = 'imports_historique';
    protected string $primaryKey = 'id_import';
    protected array $fillable = [
        'type_import', // 'Etudiants', 'Enseignants'
        'nom_fichier',
        'nb_lignes_traitees',
        'nb_succes',
        'nb_erreurs',
        'rapport_erreurs_json',
        'importe_par',
        'date_import',
    ];
}
