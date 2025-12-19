<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\Configuration;

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
        return Configuration::get($key, $default);
    }

    /**
     * Définit un paramètre de configuration
     */
    public static function set(string $key, mixed $value, ?string $type = null): void
    {
        Configuration::set($key, $value, $type);
    }

    /**
     * Récupère toutes les configurations
     */
    public static function all(): array
    {
        return Configuration::toutes();
    }
}
