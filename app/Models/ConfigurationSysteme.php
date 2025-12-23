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
     * Types de valeurs
     */
    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_JSON = 'json';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par clé
     */
    public static function findByCle(string $cle): ?self
    {
        return self::firstWhere(['cle_config' => $cle]);
    }

    /**
     * Retourne les configurations par groupe
     * @return self[]
     */
    public static function parGroupe(string $groupe): array
    {
        return self::where(['groupe_config' => $groupe]);
    }

    /**
     * Retourne les configurations modifiables via UI
     * @return self[]
     */
    public static function modifiablesUI(): array
    {
        return self::where(['modifiable_ui' => true]);
    }

    /**
     * Retourne toutes les configurations
     * @return array<string, mixed>
     */
    public static function toutes(): array
    {
        $configs = self::all();
        $result = [];
        foreach ($configs as $config) {
            $result[$config->cle_config] = $config->getValeurTypee();
        }
        return $result;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Récupère une valeur de configuration
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $config = self::firstWhere(['cle_config' => $cle]);
        if (!$config) {
            return $default;
        }

        return $config->getValeurTypee();
    }

    /**
     * Retourne la valeur typée selon le type_valeur
     */
    public function getValeurTypee(): mixed
    {
        return match ($this->type_valeur) {
            self::TYPE_INT => (int) $this->valeur_config,
            self::TYPE_FLOAT => (float) $this->valeur_config,
            self::TYPE_BOOLEAN => filter_var($this->valeur_config, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_JSON => json_decode($this->valeur_config, true),
            default => $this->valeur_config,
        };
    }

    /**
     * Définit une valeur de configuration
     * 
     * @param string $cle Clé de configuration
     * @param mixed $valeur Valeur à stocker
     * @param string $type Type de valeur (string par défaut, auto-détecté si null est passé explicitement)
     * @param string|null $groupe Groupe de configuration optionnel
     * @param string|null $description Description optionnelle pour les nouvelles configs
     * @param bool $modifiableUI Si la config peut être modifiée via l'UI (false pour les configs système)
     */
    public static function set(
        string $cle,
        mixed $valeur,
        string $type = self::TYPE_STRING,
        ?string $groupe = null,
        ?string $description = null,
        bool $modifiableUI = false
    ): void {
        // Si le type est passé explicitement comme 'string' mais la valeur n'est pas une chaîne,
        // utiliser la détection automatique
        if ($type === self::TYPE_STRING && !is_string($valeur)) {
            $type = self::determinerType($valeur);
        }

        $valeurStr = self::convertirValeur($valeur, $type);

        $config = self::firstWhere(['cle_config' => $cle]);
        if ($config) {
            $config->valeur_config = $valeurStr;
            $config->type_valeur = $type;
            if ($groupe !== null) {
                $config->groupe_config = $groupe;
            }
            if ($description !== null) {
                $config->description = $description;
            }
            $config->save();
        } else {
            $new = new self([
                'cle_config' => $cle,
                'valeur_config' => $valeurStr,
                'type_valeur' => $type,
                'groupe_config' => $groupe,
                'description' => $description ?? 'Configuration générée automatiquement pour: ' . $cle,
                'modifiable_ui' => $modifiableUI,
            ]);
            $new->save();
        }
    }

    /**
     * Détermine le type de la valeur automatiquement
     */
    private static function determinerType(mixed $valeur): string
    {
        if (is_bool($valeur)) {
            return self::TYPE_BOOLEAN;
        }
        if (is_int($valeur)) {
            return self::TYPE_INT;
        }
        if (is_float($valeur)) {
            return self::TYPE_FLOAT;
        }
        if (is_array($valeur) || is_object($valeur)) {
            return self::TYPE_JSON;
        }
        return self::TYPE_STRING;
    }

    /**
     * Convertit une valeur en chaîne selon son type
     */
    private static function convertirValeur(mixed $valeur, string $type): string
    {
        return match ($type) {
            self::TYPE_BOOLEAN => $valeur ? '1' : '0',
            self::TYPE_JSON => json_encode($valeur),
            default => (string) $valeur,
        };
    }

    /**
     * Supprime une configuration
     */
    public static function supprimer(string $cle): bool
    {
        $config = self::findByCle($cle);
        if ($config) {
            return $config->delete();
        }
        return false;
    }

    /**
     * Retourne les groupes de configuration distincts
     */
    public static function getGroupes(): array
    {
        $table = (new self())->table;
        $sql = "SELECT DISTINCT groupe_config FROM {$table} 
                WHERE groupe_config IS NOT NULL 
                ORDER BY groupe_config";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Vérifie si une clé existe
     */
    public static function existe(string $cle): bool
    {
        return self::findByCle($cle) !== null;
    }
}
