<?php

declare(strict_types=1);

namespace Src\Support;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Factory pour la création d'instances de cache
 * 
 * Fournit différents adaptateurs selon l'environnement:
 * - APCu en production (si disponible)
 * - Filesystem en développement
 * - Array pour les tests
 */
class CacheFactory
{
    private static ?CacheItemPoolInterface $instance = null;
    private static string $defaultDriver = 'file';
    private static string $cacheDir = '';
    private static int $defaultTtl = 3600;

    /**
     * Retourne l'instance de cache singleton
     */
    public static function getInstance(): CacheItemPoolInterface
    {
        if (self::$instance === null) {
            self::$instance = self::create();
        }
        return self::$instance;
    }

    /**
     * Crée une nouvelle instance de cache
     */
    public static function create(?string $driver = null): CacheItemPoolInterface
    {
        $driver = $driver ?? self::$defaultDriver;

        return match ($driver) {
            'apcu' => self::createApcuAdapter(),
            'file' => self::createFilesystemAdapter(),
            'array' => self::createArrayAdapter(),
            default => self::createFilesystemAdapter(),
        };
    }

    /**
     * Crée un adaptateur APCu
     */
    private static function createApcuAdapter(): CacheItemPoolInterface
    {
        if (!extension_loaded('apcu') || !apcu_enabled()) {
            // Fallback vers filesystem si APCu non disponible
            return self::createFilesystemAdapter();
        }

        return new ApcuAdapter(
            namespace: 'checkmaster',
            defaultLifetime: self::$defaultTtl
        );
    }

    /**
     * Crée un adaptateur Filesystem
     */
    private static function createFilesystemAdapter(): CacheItemPoolInterface
    {
        $cacheDir = self::$cacheDir ?: self::getDefaultCacheDir();

        return new FilesystemAdapter(
            namespace: 'checkmaster',
            defaultLifetime: self::$defaultTtl,
            directory: $cacheDir
        );
    }

    /**
     * Crée un adaptateur Array (pour les tests)
     */
    private static function createArrayAdapter(): CacheItemPoolInterface
    {
        return new ArrayAdapter(
            defaultLifetime: self::$defaultTtl
        );
    }

    /**
     * Configure le driver par défaut
     */
    public static function setDefaultDriver(string $driver): void
    {
        self::$defaultDriver = $driver;
        self::$instance = null; // Reset instance
    }

    /**
     * Configure le répertoire de cache
     */
    public static function setCacheDirectory(string $directory): void
    {
        self::$cacheDir = $directory;
        self::$instance = null;
    }

    /**
     * Configure le TTL par défaut
     */
    public static function setDefaultTtl(int $seconds): void
    {
        self::$defaultTtl = $seconds;
    }

    /**
     * Retourne le répertoire de cache par défaut
     */
    private static function getDefaultCacheDir(): string
    {
        // Remonter depuis src/Support jusqu'à la racine
        $rootDir = dirname(__DIR__, 3);
        return $rootDir . '/storage/cache';
    }

    /**
     * Réinitialise l'instance (pour les tests)
     */
    public static function reset(): void
    {
        self::$instance = null;
        self::$defaultDriver = 'file';
        self::$cacheDir = '';
        self::$defaultTtl = 3600;
    }

    /**
     * Raccourci pour obtenir une valeur du cache
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cache = self::getInstance();
        $item = $cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        return $default;
    }

    /**
     * Raccourci pour définir une valeur dans le cache
     */
    public static function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $cache = self::getInstance();
        $item = $cache->getItem($key);
        $item->set($value);

        if ($ttl !== null) {
            $item->expiresAfter($ttl);
        }

        return $cache->save($item);
    }

    /**
     * Raccourci pour supprimer une valeur du cache
     */
    public static function delete(string $key): bool
    {
        return self::getInstance()->deleteItem($key);
    }

    /**
     * Vide tout le cache
     */
    public static function clear(): bool
    {
        return self::getInstance()->clear();
    }

    /**
     * Obtenir ou définir avec callback (cache-aside)
     */
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = self::get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        self::set($key, $value, $ttl);

        return $value;
    }
}
