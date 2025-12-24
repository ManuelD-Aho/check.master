<?php

declare(strict_types=1);

namespace App\Services\Core;

use Hashids\Hashids;

/**
 * Service Hashids
 * 
 * Obfuscation des IDs entités pour les URLs.
 * Utilise la bibliothèque Hashids.
 * 
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceHashids
{
    private static ?Hashids $hashids = null;

    private const MIN_LENGTH = 8;
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    /**
     * Initialise le service avec une clé secrète
     */
    public static function init(?string $salt = null): void
    {
        $salt = $salt ?? getenv('HASHIDS_SALT') ?: 'checkmaster_secret_salt_2024';
        self::$hashids = new Hashids($salt, self::MIN_LENGTH, self::ALPHABET);
    }

    /**
     * Retourne l'instance Hashids
     */
    private static function getInstance(): Hashids
    {
        if (self::$hashids === null) {
            self::init();
        }
        return self::$hashids;
    }

    /**
     * Encode un ID en hash
     */
    public static function encode(int $id): string
    {
        return self::getInstance()->encode($id);
    }

    /**
     * Encode plusieurs IDs en hash
     */
    public static function encodeMultiple(int ...$ids): string
    {
        return self::getInstance()->encode(...$ids);
    }

    /**
     * Décode un hash en ID
     *
     * @return int|null L'ID décodé ou null si invalide
     */
    public static function decode(string $hash): ?int
    {
        $result = self::getInstance()->decode($hash);
        return !empty($result) ? (int) $result[0] : null;
    }

    /**
     * Décode un hash en tableau d'IDs
     *
     * @return int[] Les IDs décodés
     */
    public static function decodeMultiple(string $hash): array
    {
        return array_map('intval', self::getInstance()->decode($hash));
    }

    /**
     * Vérifie si un hash est valide
     */
    public static function isValid(string $hash): bool
    {
        return self::decode($hash) !== null;
    }

    /**
     * Encode un ID d'entité avec préfixe de type
     */
    public static function encodeEntity(string $entityType, int $id): string
    {
        $prefix = self::getEntityPrefix($entityType);
        return $prefix . '_' . self::encode($id);
    }

    /**
     * Décode un hash d'entité avec vérification du type
     *
     * @return array{type: string, id: int}|null
     */
    public static function decodeEntity(string $hash): ?array
    {
        if (!str_contains($hash, '_')) {
            return null;
        }

        [$prefix, $encodedId] = explode('_', $hash, 2);
        $id = self::decode($encodedId);

        if ($id === null) {
            return null;
        }

        $entityType = self::getEntityTypeFromPrefix($prefix);
        if ($entityType === null) {
            return null;
        }

        return [
            'type' => $entityType,
            'id' => $id,
        ];
    }

    /**
     * Retourne le préfixe pour un type d'entité
     */
    private static function getEntityPrefix(string $entityType): string
    {
        $prefixes = [
            'utilisateur' => 'usr',
            'etudiant' => 'etu',
            'enseignant' => 'ens',
            'dossier' => 'dos',
            'rapport' => 'rap',
            'soutenance' => 'sou',
            'paiement' => 'pai',
            'commission' => 'com',
            'document' => 'doc',
            'notification' => 'not',
            'message' => 'msg',
            'candidature' => 'can',
            'jury' => 'jur',
            'archive' => 'arc',
            'session' => 'ses',
        ];

        return $prefixes[$entityType] ?? substr($entityType, 0, 3);
    }

    /**
     * Retourne le type d'entité depuis un préfixe
     */
    private static function getEntityTypeFromPrefix(string $prefix): ?string
    {
        $types = [
            'usr' => 'utilisateur',
            'etu' => 'etudiant',
            'ens' => 'enseignant',
            'dos' => 'dossier',
            'rap' => 'rapport',
            'sou' => 'soutenance',
            'pai' => 'paiement',
            'com' => 'commission',
            'doc' => 'document',
            'not' => 'notification',
            'msg' => 'message',
            'can' => 'candidature',
            'jur' => 'jury',
            'arc' => 'archive',
            'ses' => 'session',
        ];

        return $types[$prefix] ?? null;
    }

    /**
     * Encode un ID pour une URL
     */
    public static function forUrl(int $id): string
    {
        return self::encode($id);
    }

    /**
     * Décode un ID depuis une URL
     */
    public static function fromUrl(string $hash): ?int
    {
        return self::decode($hash);
    }

    /**
     * Encode un ID avec un contexte (pour plus de sécurité)
     */
    public static function encodeWithContext(int $id, string $context): string
    {
        // Ajouter un checksum basé sur le contexte
        $contextHash = crc32($context) % 1000;
        return self::encodeMultiple($id, $contextHash);
    }

    /**
     * Décode un ID avec vérification du contexte
     */
    public static function decodeWithContext(string $hash, string $context): ?int
    {
        $decoded = self::decodeMultiple($hash);

        if (count($decoded) !== 2) {
            return null;
        }

        [$id, $contextHash] = $decoded;
        $expectedHash = crc32($context) % 1000;

        if ($contextHash !== $expectedHash) {
            return null;
        }

        return $id;
    }

    /**
     * Génère un token unique
     */
    public static function generateToken(): string
    {
        return self::encodeMultiple(
            time(),
            random_int(1000, 9999),
            random_int(1000, 9999)
        );
    }
}
