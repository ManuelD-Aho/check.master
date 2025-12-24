<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour les chaînes de caractères
 * 
 * Utilitaires pour la manipulation de chaînes (complémente Src\Support\Str).
 */
class StringHelper
{
    /**
     * Tronque une chaîne avec des points de suspension
     */
    public static function truncate(string $text, int $length, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - mb_strlen($suffix)) . $suffix;
    }

    /**
     * Tronque une chaîne aux mots entiers
     */
    public static function truncateWords(string $text, int $words, string $suffix = '...'): string
    {
        $wordArray = explode(' ', $text);

        if (count($wordArray) <= $words) {
            return $text;
        }

        return implode(' ', array_slice($wordArray, 0, $words)) . $suffix;
    }

    /**
     * Génère un slug à partir d'une chaîne
     */
    public static function slug(string $text, string $separator = '-'): string
    {
        // Convertir en minuscules
        $text = mb_strtolower($text);

        // Translittérer les caractères accentués
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        // Remplacer les caractères non alphanumériques par le séparateur
        $text = preg_replace('/[^a-z0-9]+/', $separator, $text) ?? '';

        // Supprimer les séparateurs en début et fin
        return trim($text, $separator);
    }

    /**
     * Convertit en camelCase
     */
    public static function camelCase(string $text): string
    {
        $text = str_replace(['-', '_', ' '], ' ', $text);
        $text = ucwords(strtolower($text));
        $text = str_replace(' ', '', $text);

        return lcfirst($text);
    }

    /**
     * Convertit en PascalCase
     */
    public static function pascalCase(string $text): string
    {
        return ucfirst(self::camelCase($text));
    }

    /**
     * Convertit en snake_case
     */
    public static function snakeCase(string $text): string
    {
        // Insérer un underscore avant les majuscules
        $text = preg_replace('/([a-z])([A-Z])/', '$1_$2', $text) ?? '';

        // Remplacer les espaces et tirets par des underscores
        $text = str_replace([' ', '-'], '_', $text);

        return strtolower($text);
    }

    /**
     * Convertit en kebab-case
     */
    public static function kebabCase(string $text): string
    {
        return str_replace('_', '-', self::snakeCase($text));
    }

    /**
     * Met en majuscule la première lettre de chaque mot
     */
    public static function titleCase(string $text): string
    {
        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Masque partiellement une chaîne
     */
    public static function mask(string $text, string $character = '*', int $start = 0, ?int $length = null): string
    {
        $textLength = mb_strlen($text);

        if ($length === null) {
            $length = $textLength - $start;
        }

        $mask = str_repeat($character, $length);

        return mb_substr($text, 0, $start) . $mask . mb_substr($text, $start + $length);
    }

    /**
     * Extrait les initiales d'un nom
     */
    public static function initials(string $name, int $maxLength = 2): string
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                if (mb_strlen($initials) >= $maxLength) {
                    break;
                }
            }
        }

        return $initials;
    }

    /**
     * Génère un nom complet à partir de prénom et nom
     */
    public static function fullName(string $firstName, string $lastName): string
    {
        return trim($firstName . ' ' . $lastName);
    }

    /**
     * Formate un nom en "NOM Prénom"
     */
    public static function formatName(string $firstName, string $lastName): string
    {
        return mb_strtoupper($lastName) . ' ' . self::titleCase($firstName);
    }

    /**
     * Vérifie si une chaîne commence par une autre
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    /**
     * Vérifie si une chaîne se termine par une autre
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }

    /**
     * Vérifie si une chaîne contient une autre
     */
    public static function contains(string $haystack, string $needle): bool
    {
        return str_contains($haystack, $needle);
    }

    /**
     * Supprime les espaces multiples
     */
    public static function normalizeWhitespace(string $text): string
    {
        return (string) preg_replace('/\s+/', ' ', trim($text));
    }

    /**
     * Extrait les nombres d'une chaîne
     */
    public static function extractNumbers(string $text): string
    {
        return (string) preg_replace('/[^0-9]/', '', $text);
    }

    /**
     * Génère une chaîne aléatoire
     */
    public static function random(int $length = 16, string $characters = ''): string
    {
        if (empty($characters)) {
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }

        $result = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }

    /**
     * Génère un UUID v4
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Compte les mots dans une chaîne
     */
    public static function wordCount(string $text): int
    {
        return str_word_count($text);
    }

    /**
     * Limite le nombre de caractères
     */
    public static function limit(string $text, int $limit, string $end = '...'): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit)) . $end;
    }

    /**
     * Remplace la première occurrence
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Remplace la dernière occurrence
     */
    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Convertit une chaîne en tableau de mots
     *
     * @return array<string>
     */
    public static function toWords(string $text): array
    {
        return array_filter(preg_split('/\s+/', trim($text)) ?: []);
    }

    /**
     * Ajoute un préfixe si absent
     */
    public static function ensurePrefix(string $text, string $prefix): string
    {
        if (!self::startsWith($text, $prefix)) {
            return $prefix . $text;
        }

        return $text;
    }

    /**
     * Ajoute un suffixe si absent
     */
    public static function ensureSuffix(string $text, string $suffix): string
    {
        if (!self::endsWith($text, $suffix)) {
            return $text . $suffix;
        }

        return $text;
    }

    /**
     * Retire un préfixe
     */
    public static function removePrefix(string $text, string $prefix): string
    {
        if (self::startsWith($text, $prefix)) {
            return mb_substr($text, mb_strlen($prefix));
        }

        return $text;
    }

    /**
     * Retire un suffixe
     */
    public static function removeSuffix(string $text, string $suffix): string
    {
        if (self::endsWith($text, $suffix)) {
            return mb_substr($text, 0, -mb_strlen($suffix));
        }

        return $text;
    }

    /**
     * Pad une chaîne à gauche
     */
    public static function padLeft(string $text, int $length, string $pad = ' '): string
    {
        return str_pad($text, $length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad une chaîne à droite
     */
    public static function padRight(string $text, int $length, string $pad = ' '): string
    {
        return str_pad($text, $length, $pad, STR_PAD_RIGHT);
    }

    /**
     * Vérifie si une chaîne est vide ou ne contient que des espaces
     */
    public static function isBlank(?string $text): bool
    {
        return $text === null || trim($text) === '';
    }

    /**
     * Retourne la chaîne ou une valeur par défaut si vide
     */
    public static function valueOrDefault(?string $text, string $default): string
    {
        return self::isBlank($text) ? $default : (string) $text;
    }
}
