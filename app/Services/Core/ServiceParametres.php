<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\ConfigurationSysteme;

/**
 * Service de gestion des paramètres de l'application
 */
class ServiceParametres
{
    /**
     * Récupère un paramètre de configuration
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return ConfigurationSysteme::get($key, $default);
    }

    /**
     * Définit un paramètre de configuration
     * 
     * @param string $key Clé de configuration
     * @param mixed $value Valeur à stocker
     * @param string|null $type Type de valeur (auto-détecté si non spécifié)
     * @param string|null $groupe Groupe de configuration optionnel
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $groupe = null): void
    {
        ConfigurationSysteme::set(
            $key,
            $value,
            $type ?? ConfigurationSysteme::TYPE_STRING,
            $groupe
        );
    }

    /**
     * Récupère toutes les configurations
     */
    public static function all(): array
    {
        return ConfigurationSysteme::toutes();
    }
}
