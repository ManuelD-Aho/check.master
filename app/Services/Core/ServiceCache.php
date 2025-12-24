<?php

declare(strict_types=1);

namespace App\Services\Core;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Service Cache
 * 
 * Gestion du cache multi-niveau (mémoire, fichier, base de données).
 * Compatible Symfony Cache.
 * 
 * @see Constitution IV - Performance
 */
class ServiceCache
{
    private static ?CacheInterface $fileCache = null;
    private static ?ArrayAdapter $memoryCache = null;

    private const DEFAULT_TTL = 3600; // 1 heure
    private const CACHE_NAMESPACE = 'checkmaster';
    private const CACHE_DIR = 'storage/cache';

    /**
     * Initialise le cache fichier
     */
    public static function init(?string $cacheDir = null): void
    {
        $dir = $cacheDir ?? dirname(__DIR__, 3) . '/' . self::CACHE_DIR;
        self::$fileCache = new FilesystemAdapter(
            self::CACHE_NAMESPACE,
            self::DEFAULT_TTL,
            $dir
        );
        self::$memoryCache = new ArrayAdapter();
    }

    /**
     * Retourne l'adaptateur de cache fichier
     */
    private static function getFileCache(): CacheInterface
    {
        if (self::$fileCache === null) {
            self::init();
        }
        return self::$fileCache;
    }

    /**
     * Retourne l'adaptateur de cache mémoire
     */
    private static function getMemoryCache(): ArrayAdapter
    {
        if (self::$memoryCache === null) {
            self::$memoryCache = new ArrayAdapter();
        }
        return self::$memoryCache;
    }

    /**
     * Récupère une valeur du cache
     *
     * @template T
     * @param string $key Clé de cache
     * @param callable(): T $callback Fonction pour générer la valeur si absente
     * @param int|null $ttl Durée de vie en secondes (null = défaut)
     * @return T
     */
    public static function get(string $key, callable $callback, ?int $ttl = null): mixed
    {
        // D'abord vérifier le cache mémoire
        $memoryCache = self::getMemoryCache();

        try {
            return $memoryCache->get($key, function () use ($key, $callback, $ttl) {
                // Si pas en mémoire, vérifier le cache fichier
                return self::getFileCache()->get($key, function (ItemInterface $item) use ($callback, $ttl) {
                    if ($ttl !== null) {
                        $item->expiresAfter($ttl);
                    }
                    return $callback();
                });
            });
        } catch (\Exception $e) {
            // En cas d'erreur de cache, exécuter le callback directement
            return $callback();
        }
    }

    /**
     * Stocke une valeur dans le cache
     */
    public static function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            $cache = self::getFileCache();
            $item = $cache->getItem($key);
            $item->set($value);

            if ($ttl !== null) {
                $item->expiresAfter($ttl);
            }

            return $cache->save($item);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Vérifie si une clé existe dans le cache
     */
    public static function has(string $key): bool
    {
        try {
            return self::getFileCache()->hasItem($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Supprime une entrée du cache
     */
    public static function delete(string $key): bool
    {
        try {
            self::getMemoryCache()->deleteItem($key);
            return self::getFileCache()->deleteItem($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Supprime plusieurs entrées du cache
     */
    public static function deleteMultiple(array $keys): bool
    {
        try {
            self::getMemoryCache()->deleteItems($keys);
            return self::getFileCache()->deleteItems($keys);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Vide tout le cache
     */
    public static function clear(): bool
    {
        try {
            self::getMemoryCache()->clear();
            return self::getFileCache()->clear();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cache une valeur simple (sans callback)
     */
    public static function remember(string $key, mixed $value, ?int $ttl = null): mixed
    {
        if (self::has($key)) {
            return self::get($key, fn() => $value);
        }

        self::set($key, $value, $ttl);
        return $value;
    }

    /**
     * Supprime les entrées correspondant à un préfixe
     */
    public static function invalidatePrefix(string $prefix): void
    {
        // Le cache Symfony ne supporte pas directement les préfixes
        // On utilise des tags ou on vide tout
        self::clear();
    }

    /**
     * Cache pour les statistiques (courte durée)
     */
    public static function stats(string $key, callable $callback): mixed
    {
        return self::get("stats_{$key}", $callback, 300); // 5 minutes
    }

    /**
     * Cache pour les configurations (longue durée)
     */
    public static function config(string $key, callable $callback): mixed
    {
        return self::get("config_{$key}", $callback, 86400); // 24 heures
    }

    /**
     * Cache pour les permissions (durée moyenne)
     */
    public static function permissions(string $key, callable $callback): mixed
    {
        return self::get("perm_{$key}", $callback, 1800); // 30 minutes
    }

    /**
     * Invalide le cache des permissions pour un utilisateur
     */
    public static function invalidateUserPermissions(int $userId): void
    {
        self::delete("perm_user_{$userId}");
    }

    /**
     * Retourne les informations de cache
     */
    public static function info(): array
    {
        $cacheDir = dirname(__DIR__, 3) . '/' . self::CACHE_DIR;

        $size = 0;
        $files = 0;

        if (is_dir($cacheDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                    $files++;
                }
            }
        }

        return [
            'directory' => $cacheDir,
            'files' => $files,
            'size_bytes' => $size,
            'size_formatted' => self::formatBytes($size),
        ];
    }

    /**
     * Formate la taille en octets
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
