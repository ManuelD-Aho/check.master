<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Str - Utilitaires pour chaînes de caractères
 * 
 * Fournit des méthodes statiques pour la manipulation de chaînes.
 */
class Str
{
    /**
     * Convertit en camelCase
     */
    public static function camel(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return lcfirst(str_replace(' ', '', $value));
    }

    /**
     * Convertit en PascalCase (StudlyCase)
     */
    public static function studly(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Convertit en snake_case
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value);
            $value = mb_strtolower($value, 'UTF-8');
        }

        return $value;
    }

    /**
     * Convertit en kebab-case
     */
    public static function kebab(string $value): string
    {
        return self::snake($value, '-');
    }

    /**
     * Convertit en slug URL-friendly
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        // Translittérer les caractères accentués
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        // Remplacer les caractères non alphanumériques par des séparateurs
        $value = preg_replace('/[^a-zA-Z0-9\s-]/', '', $value);

        // Remplacer les espaces par des séparateurs
        $value = preg_replace('/[\s-]+/', $separator, $value);

        // Supprimer les séparateurs en début/fin
        return trim($value, $separator);
    }

    /**
     * Tronque une chaîne
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit, 'UTF-8') . $end;
    }

    /**
     * Tronque par mots
     */
    public static function words(string $value, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || mb_strlen($value, 'UTF-8') === mb_strlen($matches[0], 'UTF-8')) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Vérifie si la chaîne commence par
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si la chaîne finit par
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si la chaîne contient
     */
    public static function contains(string $haystack, string|array $needles, bool $ignoreCase = false): bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack, 'UTF-8');
        }

        foreach ((array) $needles as $needle) {
            $searchNeedle = $ignoreCase ? mb_strtolower($needle, 'UTF-8') : $needle;

            if ($searchNeedle !== '' && str_contains($haystack, $searchNeedle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne la portion avant un délimiteur
     */
    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        return $pos === false ? $subject : substr($subject, 0, $pos);
    }

    /**
     * Retourne la portion après un délimiteur
     */
    public static function after(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        return $pos === false ? $subject : substr($subject, $pos + strlen($search));
    }

    /**
     * Extrait une portion entre deux délimiteurs
     */
    public static function between(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return self::before(self::after($subject, $from), $to);
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
     * Génère une chaîne aléatoire
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Génère un UUID v4
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Vérifie si UUID valide
     */
    public static function isUuid(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }

    /**
     * Masque une partie de la chaîne
     */
    public static function mask(string $string, string $character = '*', int $index = 0, ?int $length = null): string
    {
        if ($character === '') {
            return $string;
        }

        $stringLength = mb_strlen($string, 'UTF-8');
        $length = $length ?? $stringLength;

        if ($index < 0) {
            $index = max(0, $stringLength + $index);
        }

        $start = mb_substr($string, 0, $index, 'UTF-8');
        $masked = str_repeat($character, min($length, $stringLength - $index));
        $end = mb_substr($string, $index + $length, null, 'UTF-8');

        return $start . $masked . $end;
    }

    /**
     * Masque un email
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked = self::mask($name, '*', 1);
        } else {
            $masked = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        }

        return $masked . '@' . $domain;
    }

    /**
     * Formate un numéro de téléphone
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4 $5', $phone);
        }

        return $phone;
    }

    /**
     * Retire les espaces multiples
     */
    public static function squish(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value));
    }

    /**
     * Convertit en titre (majuscule première lettre de chaque mot)
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Convertit en majuscules
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convertit en minuscules
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Compte les mots
     */
    public static function wordCount(string $value): int
    {
        return str_word_count($value);
    }

    /**
     * Retourne la longueur
     */
    public static function length(string $value): int
    {
        return mb_strlen($value, 'UTF-8');
    }
}
