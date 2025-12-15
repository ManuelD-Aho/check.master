<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle PermissionAction (Cache des permissions)
 * 
 * Cache des permissions calculées pour un utilisateur.
 * Table: permissions_cache
 */
class PermissionAction extends Model
{
    protected string $table = 'permissions_cache';
    protected string $primaryKey = 'utilisateur_id'; // Composite key
    protected array $fillable = [
        'utilisateur_id',
        'ressource_code',
        'permissions_json',
        'genere_le',
        'expire_le',
    ];

    /**
     * Durée de vie du cache (5 minutes)
     */
    public const CACHE_TTL = 300;

    /**
     * Retourne les permissions depuis le cache
     */
    public static function depuisCache(int $utilisateurId, string $ressourceCode): ?array
    {
        $sql = "SELECT * FROM permissions_cache 
                WHERE utilisateur_id = :user_id AND ressource_code = :code
                AND expire_le > :now";

        $stmt = self::raw($sql, [
            'user_id' => $utilisateurId,
            'code' => $ressourceCode,
            'now' => date('Y-m-d H:i:s'),
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return json_decode($row['permissions_json'], true);
    }

    /**
     * Stocke les permissions en cache
     */
    public static function mettreEnCache(
        int $utilisateurId,
        string $ressourceCode,
        array $permissions,
        int $ttl = self::CACHE_TTL
    ): void {
        // Supprimer l'entrée existante
        $sql = "DELETE FROM permissions_cache 
                WHERE utilisateur_id = :user_id AND ressource_code = :code";
        self::raw($sql, [
            'user_id' => $utilisateurId,
            'code' => $ressourceCode,
        ]);

        // Insérer la nouvelle entrée
        $sql = "INSERT INTO permissions_cache 
                (utilisateur_id, ressource_code, permissions_json, genere_le, expire_le)
                VALUES (:user_id, :code, :perms, :now, :expire)";

        self::raw($sql, [
            'user_id' => $utilisateurId,
            'code' => $ressourceCode,
            'perms' => json_encode($permissions),
            'now' => date('Y-m-d H:i:s'),
            'expire' => date('Y-m-d H:i:s', time() + $ttl),
        ]);
    }

    /**
     * Invalide le cache d'un utilisateur
     */
    public static function invaliderUtilisateur(int $utilisateurId): int
    {
        $sql = "DELETE FROM permissions_cache WHERE utilisateur_id = :user_id";
        $stmt = self::raw($sql, ['user_id' => $utilisateurId]);
        return $stmt->rowCount();
    }

    /**
     * Invalide le cache pour une ressource
     */
    public static function invaliderRessource(string $ressourceCode): int
    {
        $sql = "DELETE FROM permissions_cache WHERE ressource_code = :code";
        $stmt = self::raw($sql, ['code' => $ressourceCode]);
        return $stmt->rowCount();
    }

    /**
     * Invalide tout le cache des permissions
     */
    public static function invaliderTout(): int
    {
        $sql = "DELETE FROM permissions_cache";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }

    /**
     * Nettoie les entrées expirées
     */
    public static function nettoyerExpirees(): int
    {
        $sql = "DELETE FROM permissions_cache WHERE expire_le < :now";
        $stmt = self::raw($sql, ['now' => date('Y-m-d H:i:s')]);
        return $stmt->rowCount();
    }

    /**
     * Vérifie si le cache existe et est valide
     */
    public static function estEnCache(int $utilisateurId, string $ressourceCode): bool
    {
        return self::depuisCache($utilisateurId, $ressourceCode) !== null;
    }
}
