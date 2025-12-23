<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour la sécurité
 * 
 * Utilitaires pour la sanitization, l'échappement et la protection XSS.
 */
class SecurityHelper
{
    /**
     * Échappe une chaîne pour affichage HTML
     */
    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Alias pour escape (fonction helper e())
     */
    public static function e(mixed $value): string
    {
        return self::escape($value);
    }

    /**
     * Échappe pour un attribut HTML
     */
    public static function escapeAttr(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Échappe pour JavaScript
     */
    public static function escapeJs(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return json_encode((string) $value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?: '""';
    }

    /**
     * Échappe pour une URL
     */
    public static function escapeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL) ?: '';
    }

    /**
     * Échappe pour CSS
     */
    public static function escapeCss(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', $value) ?? '';
    }

    /**
     * Nettoie une chaîne HTML (supprime les balises dangereuses)
     *
     * @param array<string> $allowedTags Balises HTML autorisées
     */
    public static function cleanHtml(string $html, array $allowedTags = []): string
    {
        if (empty($allowedTags)) {
            // Balises sûres par défaut
            $allowedTags = ['p', 'br', 'b', 'i', 'u', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'span'];
        }

        $allowedTagsStr = '<' . implode('><', $allowedTags) . '>';
        $cleaned = strip_tags($html, $allowedTagsStr);

        // Supprimer les attributs dangereux des balises restantes
        $cleaned = preg_replace('/(<[^>]+)\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '$1', $cleaned) ?? '';
        $cleaned = preg_replace('/(<[^>]+)\s+javascript\s*:/i', '$1', $cleaned) ?? '';

        return $cleaned;
    }

    /**
     * Génère un token CSRF
     */
    public static function generateCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Vérifie un token CSRF
     */
    public static function verifyCsrfToken(string $token, string $storedToken): bool
    {
        return hash_equals($storedToken, $token);
    }

    /**
     * Génère un mot de passe aléatoire sécurisé
     */
    public static function generatePassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        // Assurer au moins un de chaque type
        $password .= 'abcdefghijklmnopqrstuvwxyz'[random_int(0, 25)];
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[random_int(0, 25)];
        $password .= '0123456789'[random_int(0, 9)];
        $password .= '!@#$%^&*'[random_int(0, 7)];

        // Compléter avec des caractères aléatoires
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // Mélanger
        return str_shuffle($password);
    }

    /**
     * Hash un mot de passe avec Argon2id
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Vérifie un mot de passe contre son hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Vérifie si un hash a besoin d'être recalculé
     */
    public static function passwordNeedsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Génère un token sécurisé
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Génère un OTP (One-Time Password) numérique
     */
    public static function generateOtp(int $digits = 6): string
    {
        $max = (int) pow(10, $digits) - 1;
        $otp = random_int(0, $max);
        return str_pad((string) $otp, $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Masque partiellement une chaîne (email, téléphone, etc.)
     */
    public static function mask(string $value, int $visibleStart = 3, int $visibleEnd = 3): string
    {
        $length = strlen($value);

        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat('*', $length);
        }

        $start = substr($value, 0, $visibleStart);
        $end = substr($value, -$visibleEnd);
        $masked = str_repeat('*', $length - $visibleStart - $visibleEnd);

        return $start . $masked . $end;
    }

    /**
     * Masque un email
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return self::mask($email);
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        $maskedLocal = self::mask($localPart, 2, 1);
        return $maskedLocal . '@' . $domain;
    }

    /**
     * Masque un numéro de téléphone
     */
    public static function maskPhone(string $phone): string
    {
        return self::mask($phone, 4, 2);
    }

    /**
     * Sanitise une entrée pour une requête de recherche
     */
    public static function sanitizeSearchQuery(string $query): string
    {
        // Supprimer les caractères spéciaux SQL
        $query = preg_replace('/[\'";%_]/', '', $query) ?? '';

        // Limiter la longueur
        $query = substr($query, 0, 100);

        // Supprimer les espaces multiples
        $query = preg_replace('/\s+/', ' ', $query) ?? '';

        return trim($query);
    }

    /**
     * Vérifie si une URL est sûre (même domaine ou whitelist)
     *
     * @param array<string> $allowedDomains
     */
    public static function isSafeUrl(string $url, array $allowedDomains = []): bool
    {
        $parsed = parse_url($url);

        // Rejeter les schémas dangereux
        if (isset($parsed['scheme'])) {
            $dangerousSchemes = ['javascript', 'data', 'vbscript'];
            if (in_array(strtolower($parsed['scheme']), $dangerousSchemes, true)) {
                return false;
            }
        }

        // Si pas de domaines autorisés, accepter les URLs relatives
        if (empty($allowedDomains)) {
            return !isset($parsed['host']);
        }

        // Vérifier le domaine
        $host = $parsed['host'] ?? '';
        foreach ($allowedDomains as $domain) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Nettoie un nom de fichier pour éviter les injections
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename) ?? '';

        // Éviter les extensions dangereuses
        $dangerousExtensions = ['php', 'phtml', 'exe', 'bat', 'sh', 'js'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($extension, $dangerousExtensions, true)) {
            $filename .= '.txt';
        }

        return $filename;
    }

    /**
     * Valide et nettoie une adresse IP
     */
    public static function sanitizeIp(string $ip): ?string
    {
        $ip = trim($ip);

        // Gérer les IPs derrière proxy
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return $ip;
        }

        return null;
    }

    /**
     * Chiffre une donnée sensible
     */
    public static function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new \RuntimeException('Échec du chiffrement');
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Déchiffre une donnée
     */
    public static function decrypt(string $encryptedData, string $key): ?string
    {
        $data = base64_decode($encryptedData);
        if ($data === false || strlen($data) < 16) {
            return null;
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return $decrypted === false ? null : $decrypted;
    }
}
