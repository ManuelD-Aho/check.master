<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour les tableaux
 * 
 * Utilitaires pour la manipulation de tableaux (complémente Src\Support\Arr).
 */
class ArrayHelper
{
    /**
     * Fusionne deux tableaux de manière récursive
     *
     * @param array<mixed> $array1
     * @param array<mixed> $array2
     * @return array<mixed>
     */
    public static function mergeRecursive(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (isset($array1[$key]) && is_array($array1[$key]) && is_array($value)) {
                $array1[$key] = self::mergeRecursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    /**
     * Filtre un tableau par clés
     *
     * @param array<mixed> $array
     * @param array<string> $keys
     * @return array<mixed>
     */
    public static function filterByKeys(array $array, array $keys): array
    {
        return array_filter(
            $array,
            fn($key) => in_array($key, $keys, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Exclut des clés d'un tableau
     *
     * @param array<mixed> $array
     * @param array<string> $keys
     * @return array<mixed>
     */
    public static function exceptKeys(array $array, array $keys): array
    {
        return array_filter(
            $array,
            fn($key) => !in_array($key, $keys, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Convertit un objet en tableau récursivement
     *
     * @return array<mixed>
     */
    public static function fromObject(object $object): array
    {
        $array = [];

        foreach (get_object_vars($object) as $key => $value) {
            if (is_object($value)) {
                $array[$key] = self::fromObject($value);
            } elseif (is_array($value)) {
                $array[$key] = self::processArrayValues($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Traite les valeurs d'un tableau récursivement
     *
     * @param array<mixed> $array
     * @return array<mixed>
     */
    private static function processArrayValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $array[$key] = self::fromObject($value);
            } elseif (is_array($value)) {
                $array[$key] = self::processArrayValues($value);
            }
        }

        return $array;
    }

    /**
     * Indexe un tableau par une clé spécifique
     *
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function indexBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            $indexKey = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($indexKey !== null) {
                $result[$indexKey] = $item;
            }
        }

        return $result;
    }

    /**
     * Supprime les valeurs nulles d'un tableau
     *
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function removeNulls(array $array): array
    {
        return array_filter($array, fn($value) => $value !== null);
    }

    /**
     * Supprime les valeurs vides d'un tableau
     *
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function removeEmpty(array $array): array
    {
        return array_filter($array, fn($value) => $value !== null && $value !== '' && $value !== []);
    }

    /**
     * Trie un tableau multidimensionnel par plusieurs clés
     *
     * @param array<mixed> $array
     * @param array<string, string> $keys Clés et directions (asc/desc)
     * @return array<mixed>
     */
    public static function multiSort(array $array, array $keys): array
    {
        usort($array, function ($a, $b) use ($keys) {
            foreach ($keys as $key => $direction) {
                $aVal = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
                $bVal = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);

                $result = $aVal <=> $bVal;

                if ($result !== 0) {
                    return strtolower($direction) === 'desc' ? -$result : $result;
                }
            }

            return 0;
        });

        return $array;
    }

    /**
     * Convertit un tableau en CSV
     *
     * @param array<array<mixed>> $array
     * @param array<string> $headers
     */
    public static function toCsv(array $array, array $headers = [], string $delimiter = ';'): string
    {
        $output = fopen('php://temp', 'r+');
        if ($output === false) {
            return '';
        }

        // Ajouter les en-têtes
        if (!empty($headers)) {
            fputcsv($output, $headers, $delimiter);
        }

        // Ajouter les lignes
        foreach ($array as $row) {
            if (is_array($row)) {
                fputcsv($output, $row, $delimiter);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv !== false ? $csv : '';
    }

    /**
     * Parse un CSV en tableau
     *
     * @return array<array<string>>
     */
    public static function fromCsv(string $csv, string $delimiter = ';', bool $hasHeaders = true): array
    {
        $rows = [];
        $lines = explode("\n", $csv);
        $headers = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $values = str_getcsv($line, $delimiter);

            if ($hasHeaders && $index === 0) {
                $headers = $values;
                continue;
            }

            if (!empty($headers)) {
                $row = [];
                foreach ($headers as $i => $header) {
                    $row[$header] = $values[$i] ?? '';
                }
                $rows[] = $row;
            } else {
                $rows[] = $values;
            }
        }

        return $rows;
    }

    /**
     * Calcule la somme d'une colonne
     *
     * @param array<mixed> $array
     */
    public static function sumBy(array $array, string $key): float
    {
        $sum = 0;

        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? 0) : ($item->$key ?? 0);
            $sum += (float) $value;
        }

        return $sum;
    }

    /**
     * Calcule la moyenne d'une colonne
     *
     * @param array<mixed> $array
     */
    public static function averageBy(array $array, string $key): float
    {
        if (empty($array)) {
            return 0;
        }

        return self::sumBy($array, $key) / count($array);
    }

    /**
     * Trouve le maximum d'une colonne
     *
     * @param array<mixed> $array
     */
    public static function maxBy(array $array, string $key): mixed
    {
        if (empty($array)) {
            return null;
        }

        $max = null;

        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($max === null || $value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    /**
     * Trouve le minimum d'une colonne
     *
     * @param array<mixed> $array
     */
    public static function minBy(array $array, string $key): mixed
    {
        if (empty($array)) {
            return null;
        }

        $min = null;

        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($min === null || $value < $min) {
                $min = $value;
            }
        }

        return $min;
    }

    /**
     * Compte les occurrences par valeur d'une clé
     *
     * @param array<mixed> $array
     * @return array<mixed, int>
     */
    public static function countBy(array $array, string $key): array
    {
        $counts = [];

        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($value !== null) {
                $counts[$value] = ($counts[$value] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /**
     * Retourne les valeurs uniques d'une colonne
     *
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function uniqueBy(array $array, string $key): array
    {
        $values = [];

        foreach ($array as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($value !== null && !in_array($value, $values, true)) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Partitionne un tableau en chunks
     *
     * @param array<mixed> $array
     * @return array<array<mixed>>
     */
    public static function chunk(array $array, int $size): array
    {
        return array_chunk($array, $size);
    }

    /**
     * Crée un tableau à partir d'une plage
     *
     * @return array<int>
     */
    public static function range(int $start, int $end, int $step = 1): array
    {
        return range($start, $end, $step);
    }

    /**
     * Vérifie si un tableau est vide (récursivement)
     *
     * @param array<mixed> $array
     */
    public static function isDeepEmpty(array $array): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                if (!self::isDeepEmpty($value)) {
                    return false;
                }
            } elseif ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
