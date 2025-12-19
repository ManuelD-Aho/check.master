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
    protected string $primaryKey = 'id_config';
    protected array $fillable = [
        'cle_config',
        'valeur_config',
        'type_valeur',
        'groupe_config',
        'description',
        'modifiable_ui',
    ];

    /**
     * Récupère une valeur de configuration
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $config = self::firstWhere(['cle_config' => $cle]);
        if (!$config) {
            return $default;
        }

        return match ($config->type_valeur) {
            'int' => (int) $config->valeur_config,
            'float' => (float) $config->valeur_config,
            'boolean' => filter_var($config->valeur_config, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($config->valeur_config, true),
            default => $config->valeur_config,
        };
    }

    /**
     * Définit une valeur de configuration
     */
    public static function set(string $cle, mixed $valeur, string $type = 'string', ?string $groupe = null): void
    {
        $valeurStr = (string)$valeur;
        if (is_bool($valeur)) {
            $valeurStr = $valeur ? '1' : '0';
            $type = 'boolean';
        } elseif (is_array($valeur) || is_object($valeur)) {
            $valeurStr = json_encode($valeur);
            $type = 'json';
        }

        $config = self::firstWhere(['cle_config' => $cle]);
        if ($config) {
            $config->valeur_config = $valeurStr;
            $config->type_valeur = $type;
            if ($groupe) $config->groupe_config = $groupe;
            $config->save();
        } else {
            $new = new self([
                'cle_config' => $cle,
                'valeur_config' => $valeurStr,
                'description' => 'Généré automatiquement',
                'type_valeur' => $type,
                'groupe_config' => $groupe,
            ]);
            $new->save();
        }
    }
}
