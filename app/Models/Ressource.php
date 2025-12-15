<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ressource
 * 
 * Représente une ressource protégée du système.
 * Table: ressources
 */
class Ressource extends Model
{
    protected string $table = 'ressources';
    protected string $primaryKey = 'id_ressource';
    protected array $fillable = [
        'code_ressource',
        'nom_ressource',
        'description',
        'module',
    ];

    /**
     * Trouve une ressource par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ressource' => $code]);
    }

    /**
     * Retourne les ressources d'un module
     */
    public static function getByModule(string $module): array
    {
        return self::where(['module' => $module]);
    }
}
