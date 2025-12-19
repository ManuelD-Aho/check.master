<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ressource
 * 
 * Représente une ressource du système (module, entité).
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

    // ===== RELATIONS =====

    /**
     * Retourne les permissions associées
     * @return Permission[]
     */
    public function permissions(): array
    {
        return $this->hasMany(Permission::class, 'ressource_id', 'id_ressource');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve une ressource par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ressource' => $code]);
    }

    /**
     * Retourne les ressources par module
     * @return self[]
     */
    public static function parModule(string $module): array
    {
        return self::where(['module' => $module]);
    }

    /**
     * Retourne tous les modules distincts
     */
    public static function getModules(): array
    {
        $sql = "SELECT DISTINCT module FROM ressources WHERE module IS NOT NULL ORDER BY module";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée ou met à jour une ressource
     */
    public static function creerOuMaj(string $code, string $nom, ?string $description = null, ?string $module = null): self
    {
        $ressource = self::findByCode($code);

        if ($ressource === null) {
            $ressource = new self();
            $ressource->code_ressource = $code;
        }

        $ressource->nom_ressource = $nom;
        $ressource->description = $description;
        $ressource->module = $module;
        $ressource->save();

        return $ressource;
    }
}
