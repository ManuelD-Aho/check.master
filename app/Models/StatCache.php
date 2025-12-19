<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle StatCache
 * 
 * Cache des statistiques lourdes (dashboard, rapports).
 * Table: stats_cache
 */
class StatCache extends Model
{
    protected string $table = 'stats_cache';
    protected string $primaryKey = 'id_stat';
    protected array $fillable = [
        'cle_stat',
        'valeur_json',
        'expire_le',
    ];

    /**
     * Durée par défaut du cache (en secondes)
     */
    public const TTL_DEFAUT = 3600; // 1 heure
    public const TTL_COURT = 300;   // 5 minutes
    public const TTL_LONG = 86400;  // 24 heures

    // ===== MÉTHODES DE CACHE =====

    /**
     * Récupère une stat en cache
     */
    public static function get(string $cle): ?array
    {
        $sql = "SELECT * FROM stats_cache WHERE cle_stat = :cle AND expire_le > NOW()";
        $stmt = self::raw($sql, ['cle' => $cle]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false || $row === null) {
            return null;
        }

        return json_decode($row['valeur_json'], true);
    }

    /**
     * Définit une stat en cache
     */
    public static function set(string $cle, array $valeur, int $ttlSecondes = self::TTL_DEFAUT): void
    {
        $expireLe = date('Y-m-d H:i:s', time() + $ttlSecondes);

        // Supprimer l'ancienne valeur si elle existe
        $sql = "DELETE FROM stats_cache WHERE cle_stat = :cle";
        self::raw($sql, ['cle' => $cle]);

        // Insérer la nouvelle valeur
        $cache = new self([
            'cle_stat' => $cle,
            'valeur_json' => json_encode($valeur),
            'expire_le' => $expireLe,
        ]);
        $cache->save();
    }

    /**
     * Récupère ou calcule une stat
     */
    public static function getOuCalculer(string $cle, callable $callback, int $ttl = self::TTL_DEFAUT): array
    {
        $valeur = self::get($cle);
        if ($valeur !== null) {
            return $valeur;
        }

        // Calculer et mettre en cache
        $valeur = $callback();
        self::set($cle, $valeur, $ttl);
        return $valeur;
    }

    /**
     * Supprime une stat du cache
     */
    public static function supprimer(string $cle): bool
    {
        $sql = "DELETE FROM stats_cache WHERE cle_stat = :cle";
        $stmt = self::raw($sql, ['cle' => $cle]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime les stats expirées
     */
    public static function nettoyer(): int
    {
        $sql = "DELETE FROM stats_cache WHERE expire_le <= NOW()";
        $stmt = self::raw($sql);
        return $stmt->rowCount();
    }

    /**
     * Vide tout le cache
     */
    public static function vider(): int
    {
        $sql = "TRUNCATE TABLE stats_cache";
        self::raw($sql);
        return 1;
    }

    /**
     * Vérifie si une clé est en cache
     */
    public static function existe(string $cle): bool
    {
        return self::get($cle) !== null;
    }

    /**
     * Prolonge la durée de vie d'une stat
     */
    public static function prolonger(string $cle, int $ttlSecondes = self::TTL_DEFAUT): bool
    {
        $expireLe = date('Y-m-d H:i:s', time() + $ttlSecondes);
        $sql = "UPDATE stats_cache SET expire_le = :expire WHERE cle_stat = :cle";
        $stmt = self::raw($sql, ['cle' => $cle, 'expire' => $expireLe]);
        return $stmt->rowCount() > 0;
    }
}
