<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Arr - Utilitaires pour tableaux
 * 
 * Fournit des méthodes statiques pour la manipulation de tableaux
 * avec support de la notation pointée (dot notation).
 */
class Arr
{
    /**
     * Récupère une valeur avec notation pointée
     *
     * @param array $array Tableau source
     * @param string $key Clé avec notation pointée (ex: 'user.name')
     * @param mixed $default Valeur par défaut
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Définit une valeur avec notation pointée
     */
    public static function set(array &$array, string $key, mixed $value): array
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }
                $current = &$current[$segment];
            }
        }

        return $array;
    }

    /**
     * Vérifie si une clé existe (notation pointée)
     */
    public static function has(array $array, string $key): bool
    {
        if (empty($array) || $key === '') {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Supprime une clé (notation pointée)
     */
    public static function forget(array &$array, string $key): void
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                unset($current[$segment]);
            } elseif (isset($current[$segment]) && is_array($current[$segment])) {
                $current = &$current[$segment];
            } else {
                return;
            }
        }
    }

    /**
     * Extrait uniquement certaines clés
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Exclut certaines clés
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Aplatit un tableau multidimensionnel
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } elseif ($depth === 1) {
                $result = array_merge($result, array_values($item));
            } else {
                $result = array_merge($result, self::flatten($item, $depth - 1));
            }
        }

        return $result;
    }

    /**
     * Convertit en notation pointée
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $result = array_merge($result, self::dot($value, $prepend . $key . '.'));
            } else {
                $result[$prepend . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Convertit depuis notation pointée vers tableau imbriqué
     */
    public static function undot(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            self::set($result, $key, $value);
        }

        return $result;
    }

    /**
     * Retourne le premier élément
     */
    public static function first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        if ($callback === null) {
            return reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Retourne le dernier élément
     */
    public static function last(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        if ($callback === null) {
            return end($array);
        }

        return self::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Extrait une colonne
     */
    public static function pluck(array $array, string $value, ?string $key = null): array
    {
        $result = [];

        foreach ($array as $item) {
            $itemValue = is_array($item) ? ($item[$value] ?? null) : ($item->$value ?? null);

            if ($key !== null) {
                $itemKey = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
                $result[$itemKey] = $itemValue;
            } else {
                $result[] = $itemValue;
            }
        }

        return $result;
    }

    /**
     * Groupe par une clé
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * Trie par une clé
     */
    public static function sortBy(array $array, string $key, bool $descending = false): array
    {
        usort($array, function ($a, $b) use ($key, $descending) {
            $aVal = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
            $bVal = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);

            $result = $aVal <=> $bVal;
            return $descending ? -$result : $result;
        });

        return $array;
    }

    /**
     * Retourne un élément aléatoire
     */
    public static function random(array $array, ?int $count = null): mixed
    {
        if (empty($array)) {
            return $count === null ? null : [];
        }

        if ($count === null) {
            return $array[array_rand($array)];
        }

        if ($count >= count($array)) {
            shuffle($array);
            return $array;
        }

        $keys = array_rand($array, $count);
        return array_map(fn($key) => $array[$key], (array) $keys);
    }

    /**
     * Mélange le tableau
     */
    public static function shuffle(array $array): array
    {
        shuffle($array);
        return $array;
    }

    /**
     * Vérifie si le tableau est associatif
     */
    public static function isAssoc(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Vérifie si le tableau est une liste (indices numériques séquentiels)
     */
    public static function isList(array $array): bool
    {
        return !self::isAssoc($array);
    }

    /**
     * Enveloppe une valeur dans un tableau si ce n'est pas déjà un tableau
     */
    public static function wrap(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * Vérifie si toutes les clés existent
     */
    public static function hasAll(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!self::has($array, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si au moins une clé existe
     */
    public static function hasAny(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (self::has($array, $key)) {
                return true;
            }
        }

        return false;
    }
}
