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
     */
    public static function set(string $key, mixed $value, ?string $type = null): void
    {
        ConfigurationSysteme::set($key, $value, $type);
    }

    /**
     * Récupère toutes les configurations
     */
    public static function all(): array
    {
        return ConfigurationSysteme::toutes();
    }
}
