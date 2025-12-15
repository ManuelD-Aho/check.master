<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Fonction
 * 
 * Représente une fonction (Directeur, Chef de département, etc.).
 * Table: fonctions
 */
class Fonction extends Model
{
    protected string $table = 'fonctions';
    protected string $primaryKey = 'id_fonction';
    protected array $fillable = [
        'lib_fonction',
        'description',
        'actif',
    ];

    /**
     * Trouve une fonction par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_fonction' => $libelle]);
    }

    /**
     * Retourne toutes les fonctions actives
     *
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }
}
