<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ressource
 * 
 * Représente une ressource protégée du système (module, entité).
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
     *
     * @return self[]
     */
    public static function parModule(string $module): array
    {
        return self::where(['module' => $module]);
    }

    /**
     * Retourne toutes les ressources groupées par module
     */
    public static function groupeesParModule(): array
    {
        $ressources = self::all();
        $grouped = [];

        foreach ($ressources as $ressource) {
            $module = $ressource->module ?? 'Autre';
            $grouped[$module][] = $ressource;
        }

        return $grouped;
    }

    /**
     * Retourne les permissions associées à cette ressource
     *
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return Permission::where(['ressource_id' => $this->getId()]);
    }

    /**
     * Crée une nouvelle ressource si elle n'existe pas
     */
    public static function creerSiAbsent(
        string $code,
        string $nom,
        ?string $description = null,
        ?string $module = null
    ): self {
        $existing = self::findByCode($code);
        if ($existing !== null) {
            return $existing;
        }

        $ressource = new self([
            'code_ressource' => $code,
            'nom_ressource' => $nom,
            'description' => $description,
            'module' => $module,
        ]);
        $ressource->save();
        return $ressource;
    }
}
