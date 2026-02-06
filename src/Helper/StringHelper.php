<?php

declare(strict_types=1);

namespace App\Helper;

final class StringHelper
{
    private function __construct()
    {
    }

    public static function slugify(string $text): string
    {
        $text = self::removeAccents($text);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }

    public static function truncate(string $text, int $length, string $suffix = '...'): string
    {
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
    }

    public static function removeAccents(string $text): string
    {
        if (function_exists('transliterator_transliterate')) {
            $result = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $text);

            return $result !== false ? $result : $text;
        }

        $map = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a',
            'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'é' => 'e',
            'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'õ' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ñ' => 'n', 'ç' => 'c',
            'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Á' => 'A', 'Ã' => 'A',
            'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'É' => 'E',
            'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Í' => 'I',
            'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Ó' => 'O', 'Õ' => 'O',
            'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ú' => 'U',
            'Ý' => 'Y',
            'Ñ' => 'N', 'Ç' => 'C',
        ];

        return strtr($text, $map);
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return str_contains($haystack, $needle);
    }

    public static function sanitize(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    public static function generateRandom(int $length = 16): string
    {
        $bytes = random_bytes((int) ceil($length / 2));

        return substr(bin2hex($bytes), 0, $length);
    }

    public static function mask(string $value, int $visibleStart = 2, int $visibleEnd = 2, string $char = '*'): string
    {
        $len = mb_strlen($value, 'UTF-8');

        if ($len <= $visibleStart + $visibleEnd) {
            return $value;
        }

        $start  = mb_substr($value, 0, $visibleStart, 'UTF-8');
        $end    = mb_substr($value, -$visibleEnd, null, 'UTF-8');
        $middle = str_repeat($char, $len - $visibleStart - $visibleEnd);

        return $start . $middle . $end;
    }

    public static function initials(string $name): string
    {
        $words   = preg_split('/\s+/', trim($name));
        $letters = '';

        foreach ($words as $word) {
            if ($word !== '') {
                $letters .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
            }
        }

        return $letters;
    }
}
