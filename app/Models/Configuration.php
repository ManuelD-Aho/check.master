<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Configuration
 * 
 * Représente une configuration système stockée en base.
 * Table: configurations
 */
class Configuration extends Model
{
    protected string $table = 'configurations';
    protected string $primaryKey = 'id_config';
    protected array $fillable = [
        'cle',
        'valeur',
        'type_valeur',
        'description',
        'modifiable',
    ];

    /**
     * Types de valeur
     */
    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'bool';
    public const TYPE_JSON = 'json';

    /**
     * Cache en mémoire
     */
    private static array $cache = [];

    /**
     * Récupère une valeur de configuration
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        // Vérifier le cache
        if (isset(self::$cache[$cle])) {
            return self::$cache[$cle];
        }

        $config = self::firstWhere(['cle' => $cle]);

        if ($config === null) {
            return $default;
        }

        $valeur = self::casterValeur($config->valeur, $config->type_valeur);
        self::$cache[$cle] = $valeur;

        return $valeur;
    }

    /**
     * Définit une valeur de configuration
     */
    public static function set(string $cle, mixed $valeur, ?string $type = null): void
    {
        $config = self::firstWhere(['cle' => $cle]);

        if ($config === null) {
            $config = new self([
                'cle' => $cle,
                'type_valeur' => $type ?? self::detecterType($valeur),
                'modifiable' => true,
            ]);
        }

        if (!$config->modifiable) {
            throw new \RuntimeException("La configuration '{$cle}' n'est pas modifiable");
        }

        $config->valeur = self::serialiserValeur($valeur);
        $config->save();

        // Mettre à jour le cache
        self::$cache[$cle] = $valeur;
    }

    /**
     * Caste la valeur selon son type
     */
    private static function casterValeur(string $valeur, string $type): mixed
    {
        return match ($type) {
            self::TYPE_INT => (int) $valeur,
            self::TYPE_FLOAT => (float) $valeur,
            self::TYPE_BOOL => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_JSON => json_decode($valeur, true),
            default => $valeur,
        };
    }

    /**
     * Sérialise la valeur pour stockage
     */
    private static function serialiserValeur(mixed $valeur): string
    {
        if (is_array($valeur) || is_object($valeur)) {
            return json_encode($valeur);
        }
        if (is_bool($valeur)) {
            return $valeur ? '1' : '0';
        }
        return (string) $valeur;
    }

    /**
     * Détecte le type d'une valeur
     */
    private static function detecterType(mixed $valeur): string
    {
        return match (true) {
            is_int($valeur) => self::TYPE_INT,
            is_float($valeur) => self::TYPE_FLOAT,
            is_bool($valeur) => self::TYPE_BOOL,
            is_array($valeur) || is_object($valeur) => self::TYPE_JSON,
            default => self::TYPE_STRING,
        };
    }

    /**
     * Retourne toutes les configurations
     */
    public static function toutes(): array
    {
        $configs = self::all();
        $result = [];

        foreach ($configs as $config) {
            $result[$config->cle] = [
                'valeur' => self::casterValeur($config->valeur, $config->type_valeur),
                'type' => $config->type_valeur,
                'description' => $config->description,
                'modifiable' => (bool) $config->modifiable,
            ];
        }

        return $result;
    }

    /**
     * Vide le cache
     */
    public static function viderCache(): void
    {
        self::$cache = [];
    }

    /**
     * Précharge les configurations en cache
     */
    public static function precharger(): void
    {
        $configs = self::all();
        foreach ($configs as $config) {
            self::$cache[$config->cle] = self::casterValeur($config->valeur, $config->type_valeur);
        }
    }
}
