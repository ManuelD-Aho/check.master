<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle ConfigurationSysteme
 * 
 * Stocke la configuration globale de l'application.
 * Table: configuration_systeme
 */
class ConfigurationSysteme extends Model
{
    protected string $table = 'configuration_systeme';
    protected string $primaryKey = 'cle'; // Clé naturelle possible ou id
    protected array $fillable = [
        'cle',
        'valeur',
        'description',
        'type_valeur', // 'string', 'int', 'bool', 'json'
    ];

    /**
     * Récupère une valeur de configuration
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $config = self::firstWhere(['cle' => $cle]);
        if (!$config) {
            return $default;
        }

        return match ($config->type_valeur) {
            'int' => (int) $config->valeur,
            'float' => (float) $config->valeur,
            'bool' => filter_var($config->valeur, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($config->valeur, true),
            default => $config->valeur,
        };
    }

    /**
     * Définit une valeur de configuration
     */
    public static function set(string $cle, mixed $valeur, string $type = 'string'): void
    {
        $valeurStr = $valeur;
        if (is_bool($valeur)) {
            $valeurStr = $valeur ? '1' : '0';
            $type = 'bool';
        } elseif (is_array($valeur) || is_object($valeur)) {
            $valeurStr = json_encode($valeur);
            $type = 'json';
        }

        $config = self::firstWhere(['cle' => $cle]);
        if ($config) {
            $config->valeur = (string) $valeurStr;
            $config->type_valeur = $type;
            $config->save();
        } else {
            $new = new self([
                'cle' => $cle,
                'valeur' => (string) $valeurStr,
                'description' => 'Auto-generated',
                'type_valeur' => $type,
            ]);
            $new->save();
        }
    }
}
