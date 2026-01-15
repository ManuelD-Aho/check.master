<?php

declare(strict_types=1);

/**
 * Fonctions helpers globales CheckMaster
 */

if (!function_exists('e')) {
    /**
     * Échappe une chaîne pour affichage HTML
     */
    function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('config')) {
    /**
     * Récupère une valeur de configuration depuis la base de données
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        try {
            $result = \App\Services\Core\ServiceParametres::get($key, $default);
            $cache[$key] = $result;
            return $result;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirige vers une URL
     */
    function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('url')) {
    /**
     * Génère une URL absolue
     */
    function url(string $path = ''): string
    {
        $baseUrl = config('app.url', '/check.master');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Génère une URL pour un asset
     */
    function asset(string $path): string
    {
        return '/public/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Retourne un champ hidden CSRF
     */
    function csrf_field(): string
    {
        return \Src\Support\CSRF::field();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Retourne le token CSRF actuel
     */
    function csrf_token(): string
    {
        return \Src\Support\CSRF::token();
    }
}

if (!function_exists('auth')) {
    /**
     * Retourne l'utilisateur connecté ou null
     */
    function auth(): ?\App\Models\Utilisateur
    {
        return \Src\Support\Auth::user();
    }
}

if (!function_exists('auth_id')) {
    /**
     * Retourne l'ID de l'utilisateur connecté
     */
    function auth_id(): ?int
    {
        return \Src\Support\Auth::id();
    }
}

if (!function_exists('is_authenticated')) {
    /**
     * Vérifie si un utilisateur est connecté
     */
    function is_authenticated(): bool
    {
        return \Src\Support\Auth::check();
    }
}

if (!function_exists('has_permission')) {
    /**
     * Vérifie si l'utilisateur a une permission
     */
    function has_permission(string $ressource, string $action): bool
    {
        return \Src\Support\Auth::hasPermission($ressource, $action);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die - pour debug
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('now')) {
    /**
     * Retourne une instance DateTimeImmutable pour maintenant
     */
    function now(string|DateTimeZone|null $tz = null): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $tz instanceof DateTimeZone ? $tz : null);
    }
}

if (!function_exists('env')) {
    /**
     * Récupère une variable d'environnement
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}
