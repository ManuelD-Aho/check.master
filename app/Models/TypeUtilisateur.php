<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle TypeUtilisateur
 * 
 * Référentiel des types d'utilisateurs (Admin, Enseignant, Etudiant).
 * Table: types_utilisateur
 */
class TypeUtilisateur extends Model
{
    protected string $table = 'types_utilisateur';
    protected string $primaryKey = 'id_type';
    protected array $fillable = [
        'libelle',
        'code', // 'ADMIN', 'ENS', 'ETU'
    ];

    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code' => $code]);
    }
}
