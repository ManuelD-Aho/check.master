<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle PermissionCache
 * 
 * Cache des permissions utilisateur pour performance.
 * Table: permissions_cache
 */
class PermissionCache extends Model
{
    protected string $table = 'permissions_cache';
    protected string $primaryKey = 'utilisateur_id'; // Clé composite
    protected array $fillable = [
        'utilisateur_id',
        'ressource_code',
        'permissions_json',
        'genere_le',
        'expire_le',
    ];

    /**
     * TTL par défaut du cache (5 minutes)
     */
    public const TTL_SECONDES = 300;

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve le cache pour un utilisateur et une ressource
     */
    public static function trouver(int $utilisateurId, string $ressourceCode): ?self
    {
        return self::firstWhere([
            'utilisateur_id' => $utilisateurId,
            'ressource_code' => $ressourceCode,
        ]);
    }

    /**
     * Trouve le cache valide
     */
    public static function trouverValide(int $utilisateurId, string $ressourceCode): ?self
    {
        $sql = "SELECT * FROM permissions_cache 
                WHERE utilisateur_id = :uid 
                AND ressource_code = :code 
                AND expire_le > NOW()
                LIMIT 1";

        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'code' => $ressourceCode]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le cache est valide
     */
    public function estValide(): bool
    {
        if ($this->expire_le === null) {
            return false;
        }
        return strtotime($this->expire_le) > time();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les permissions JSON décodées
     */
    public function getPermissions(): array
    {
        if (empty($this->permissions_json)) {
            return [];
        }
        return json_decode($this->permissions_json, true) ?? [];
    }

    /**
     * Définit le cache pour un utilisateur et une ressource
     */
    public static function definir(int $utilisateurId, string $ressourceCode, array $permissions): self
    {
        // Supprimer l'ancien cache
        $sql = "DELETE FROM permissions_cache 
                WHERE utilisateur_id = :uid AND ressource_code = :code";
        self::raw($sql, ['uid' => $utilisateurId, 'code' => $ressourceCode]);

        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'ressource_code' => $ressourceCode,
            'permissions_json' => json_encode($permissions),
            'genere_le' => date('Y-m-d H:i:s'),
            'expire_le' => date('Y-m-d H:i:s', time() + self::TTL_SECONDES),
        ]);
        $model->save();

        return $model;
    }

    /**
     * Invalide le cache d'un utilisateur
     */
    public static function invaliderPourUtilisateur(int $utilisateurId): int
    {
        $sql = "DELETE FROM permissions_cache WHERE utilisateur_id = :id";
        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        return $stmt->rowCount();
    }

    /**
     * Invalide le cache pour une ressource (tous utilisateurs)
     */
    public static function invaliderPourRessource(string $ressourceCode): int
    {
        $sql = "DELETE FROM permissions_cache WHERE ressource_code = :code";
        $stmt = self::raw($sql, ['code' => $ressourceCode]);
        return $stmt->rowCount();
    }

    /**
     * Invalide tout le cache
     */
    public static function invaliderTout(): int
    {
        $sql = "DELETE FROM permissions_cache";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }

    /**
     * Nettoie les caches expirés
     */
    public static function nettoyerExpires(): int
    {
        $sql = "DELETE FROM permissions_cache WHERE expire_le < NOW()";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }
}
