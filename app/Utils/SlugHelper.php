<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour les slugs
 * 
 * Utilitaires pour la génération de slugs URL-friendly.
 */
class SlugHelper
{
    /**
     * Caractères de remplacement pour les accents
     */
    private const TRANSLITERATIONS = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'Ç' => 'C', 'ç' => 'c',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ñ' => 'N', 'ñ' => 'n',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
        'Œ' => 'OE', 'œ' => 'oe',
        'Æ' => 'AE', 'æ' => 'ae',
        'ß' => 'ss',
    ];

    /**
     * Génère un slug à partir d'une chaîne
     */
    public static function generate(string $text, string $separator = '-'): string
    {
        // Translittérer les caractères accentués
        $text = strtr($text, self::TRANSLITERATIONS);

        // Essayer iconv si disponible
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($transliterated !== false) {
            $text = $transliterated;
        }

        // Convertir en minuscules
        $text = strtolower($text);

        // Remplacer les caractères non alphanumériques
        $text = preg_replace('/[^a-z0-9]+/', $separator, $text) ?? '';

        // Supprimer les séparateurs en début et fin
        return trim($text, $separator);
    }

    /**
     * Génère un slug unique avec suffixe si nécessaire
     *
     * @param callable $existsCallback Fonction pour vérifier si le slug existe
     */
    public static function generateUnique(string $text, callable $existsCallback, string $separator = '-'): string
    {
        $baseSlug = self::generate($text, $separator);
        $slug = $baseSlug;
        $counter = 1;

        while ($existsCallback($slug)) {
            $slug = $baseSlug . $separator . $counter;
            $counter++;

            // Sécurité anti-boucle infinie
            if ($counter > 1000) {
                $slug = $baseSlug . $separator . bin2hex(random_bytes(4));
                break;
            }
        }

        return $slug;
    }

    /**
     * Génère un slug avec date
     */
    public static function generateWithDate(string $text, ?string $date = null, string $separator = '-'): string
    {
        $dateStr = $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $slug = self::generate($text, $separator);

        return $dateStr . $separator . $slug;
    }

    /**
     * Génère un slug avec ID
     */
    public static function generateWithId(string $text, int $id, string $separator = '-'): string
    {
        $slug = self::generate($text, $separator);
        return $slug . $separator . $id;
    }

    /**
     * Extrait l'ID d'un slug (format: slug-123)
     */
    public static function extractId(string $slug, string $separator = '-'): ?int
    {
        $parts = explode($separator, $slug);
        $lastPart = end($parts);

        if (is_numeric($lastPart)) {
            return (int) $lastPart;
        }

        return null;
    }

    /**
     * Normalise un slug existant
     */
    public static function normalize(string $slug, string $separator = '-'): string
    {
        // Remplacer les anciens séparateurs
        $slug = str_replace(['_', ' ', '.'], $separator, $slug);

        // Convertir en minuscules
        $slug = strtolower($slug);

        // Supprimer les caractères non autorisés
        $slug = preg_replace('/[^a-z0-9' . preg_quote($separator, '/') . ']+/', '', $slug) ?? '';

        // Supprimer les séparateurs multiples
        $slug = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $slug) ?? '';

        return trim($slug, $separator);
    }

    /**
     * Vérifie si un slug est valide
     */
    public static function isValid(string $slug, string $separator = '-'): bool
    {
        // Un slug valide: minuscules, chiffres et séparateurs uniquement
        $pattern = '/^[a-z0-9' . preg_quote($separator, '/') . ']+$/';

        if (!preg_match($pattern, $slug)) {
            return false;
        }

        // Pas de séparateurs en début ou fin
        if (str_starts_with($slug, $separator) || str_ends_with($slug, $separator)) {
            return false;
        }

        // Pas de séparateurs consécutifs
        if (str_contains($slug, $separator . $separator)) {
            return false;
        }

        return true;
    }

    /**
     * Génère un slug pour un fichier
     */
    public static function generateFilename(string $originalName, string $separator = '-'): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        $slug = self::generate($baseName, $separator);

        // Limiter la longueur
        $slug = substr($slug, 0, 100);

        return $slug . '.' . $extension;
    }

    /**
     * Génère un slug pour un titre de thème de mémoire
     */
    public static function generateThemeSlug(string $theme, int $etudiantId): string
    {
        // Limiter le thème à 50 caractères pour le slug
        $shortTheme = mb_substr($theme, 0, 50);
        $slug = self::generate($shortTheme);

        return $slug . '-' . $etudiantId;
    }

    /**
     * Génère un slug pour une année académique
     */
    public static function generateAcademicYearSlug(string $year): string
    {
        // Format: 2024-2025 -> 2024-25
        $parts = explode('-', $year);
        if (count($parts) === 2) {
            return $parts[0] . '-' . substr($parts[1], -2);
        }

        return self::generate($year);
    }

    /**
     * Tronque un slug à une longueur maximale sans couper les mots
     */
    public static function truncate(string $slug, int $maxLength, string $separator = '-'): string
    {
        if (strlen($slug) <= $maxLength) {
            return $slug;
        }

        $parts = explode($separator, $slug);
        $result = '';

        foreach ($parts as $part) {
            $new = $result . ($result ? $separator : '') . $part;
            if (strlen($new) > $maxLength) {
                break;
            }
            $result = $new;
        }

        // Si même le premier mot est trop long, tronquer brutalement
        if (empty($result)) {
            return substr($slug, 0, $maxLength);
        }

        return $result;
    }

    /**
     * Convertit un slug en titre lisible
     */
    public static function toTitle(string $slug, string $separator = '-'): string
    {
        $words = explode($separator, $slug);
        $words = array_map('ucfirst', $words);

        return implode(' ', $words);
    }
}
