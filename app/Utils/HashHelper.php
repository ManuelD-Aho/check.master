<?php

declare(strict_types=1);

namespace App\Utils;

use Hashids\Hashids;

/**
 * Helper pour le hachage et l'encodage
 * 
 * Utilitaires pour les hash, IDs encodés, etc.
 */
class HashHelper
{
    private static ?Hashids $hashids = null;

    /**
     * Retourne l'instance Hashids
     */
    private static function getHashids(): Hashids
    {
        if (self::$hashids === null) {
            $salt = $_ENV['HASHIDS_SALT'] ?? 'checkmaster-default-salt';
            $minLength = (int) ($_ENV['HASHIDS_MIN_LENGTH'] ?? 8);
            self::$hashids = new Hashids($salt, $minLength);
        }

        return self::$hashids;
    }

    /**
     * Encode un ID en hash URL-safe
     */
    public static function encodeId(int $id): string
    {
        return self::getHashids()->encode($id);
    }

    /**
     * Décode un hash en ID
     */
    public static function decodeId(string $hash): ?int
    {
        $result = self::getHashids()->decode($hash);
        return $result[0] ?? null;
    }

    /**
     * Encode plusieurs IDs
     */
    public static function encodeIds(array $ids): string
    {
        return self::getHashids()->encode(...$ids);
    }

    /**
     * Décode vers plusieurs IDs
     */
    public static function decodeIds(string $hash): array
    {
        return self::getHashids()->decode($hash);
    }

    /**
     * Génère un hash SHA256
     */
    public static function sha256(string $data): string
    {
        return hash('sha256', $data);
    }

    /**
     * Génère un hash MD5 (pour checksums simples)
     */
    public static function md5(string $data): string
    {
        return md5($data);
    }

    /**
     * Hash un mot de passe avec Argon2id
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Vérifie un mot de passe
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Vérifie si un hash doit être récalculé
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID);
    }

    /**
     * Génère un token aléatoire sécurisé
     */
    public static function randomToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Génère un token URL-safe
     */
    public static function randomUrlToken(int $length = 32): string
    {
        $bytes = random_bytes($length);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Hash un fichier
     */
    public static function hashFile(string $path, string $algo = 'sha256'): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        return hash_file($algo, $path);
    }

    /**
     * Vérifie l'intégrité d'un fichier
     */
    public static function verifyFile(string $path, string $expectedHash, string $algo = 'sha256'): bool
    {
        $actualHash = self::hashFile($path, $algo);
        return $actualHash !== null && hash_equals($expectedHash, $actualHash);
    }

    /**
     * HMAC pour signature
     */
    public static function hmac(string $data, string $key, string $algo = 'sha256'): string
    {
        return hash_hmac($algo, $data, $key);
    }

    /**
     * Vérifie un HMAC
     */
    public static function verifyHmac(string $data, string $signature, string $key, string $algo = 'sha256'): bool
    {
        $expected = self::hmac($data, $key, $algo);
        return hash_equals($expected, $signature);
    }
}
