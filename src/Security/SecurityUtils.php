<?php

declare(strict_types=1);

namespace Src\Security;

use Src\Exceptions\ValidationException;

/**
 * Security Utils - Utilitaires de sécurité
 * 
 * Fonctionnalités:
 * - Protection XSS
 * - Prévention SQL Injection
 * - CSRF Protection
 * - Input sanitization
 * - Output encoding
 * - Password hashing
 * - Token generation
 * - Validation sécurisée
 * 
 * @package Src\Security
 */
class SecurityUtils
{
    /**
     * Échapper pour HTML (protection XSS)
     *
     * @param mixed $value Valeur
     * @param string $encoding Encoding
     * @return string Valeur échappée
     */
    public static function escapeHtml($value, string $encoding = 'UTF-8'): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, $encoding);
    }

    /**
     * Échapper pour attributs HTML
     *
     * @param mixed $value Valeur
     * @return string Valeur échappée
     */
    public static function escapeHtmlAttr($value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Échapper pour JavaScript
     *
     * @param mixed $value Valeur
     * @return string Valeur échappée
     */
    public static function escapeJs($value): string
    {
        if ($value === null) {
            return '';
        }

        return json_encode((string) $value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Échapper pour URL
     *
     * @param string $value Valeur
     * @return string Valeur échappée
     */
    public static function escapeUrl(string $value): string
    {
        return urlencode($value);
    }

    /**
     * Nettoyer une chaîne (supprimer tags HTML)
     *
     * @param string $value Valeur
     * @param array $allowedTags Tags autorisés
     * @return string Valeur nettoyée
     */
    public static function sanitizeString(string $value, array $allowedTags = []): string
    {
        if (empty($allowedTags)) {
            return strip_tags($value);
        }

        $allowedTagsStr = '<' . implode('><', $allowedTags) . '>';
        return strip_tags($value, $allowedTagsStr);
    }

    /**
     * Valider et nettoyer un email
     *
     * @param string $email Email
     * @return string|null Email valide ou null
     */
    public static function sanitizeEmail(string $email): ?string
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Valider et nettoyer une URL
     *
     * @param string $url URL
     * @return string|null URL valide ou null
     */
    public static function sanitizeUrl(string $url): ?string
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Nettoyer un entier
     *
     * @param mixed $value Valeur
     * @return int|null Entier ou null
     */
    public static function sanitizeInt($value): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? (int) $filtered : null;
    }

    /**
     * Nettoyer un float
     *
     * @param mixed $value Valeur
     * @return float|null Float ou null
     */
    public static function sanitizeFloat($value): ?float
    {
        $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
        return $filtered !== false ? (float) $filtered : null;
    }

    /**
     * Nettoyer un boolean
     *
     * @param mixed $value Valeur
     * @return bool Boolean
     */
    public static function sanitizeBool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Hacher un mot de passe
     *
     * @param string $password Mot de passe
     * @return string Hash
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Vérifier un mot de passe
     *
     * @param string $password Mot de passe
     * @param string $hash Hash
     * @return bool Valide
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Vérifier si un hash doit être re-haché
     *
     * @param string $hash Hash
     * @return bool Doit être re-haché
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Générer un token aléatoire sécurisé
     *
     * @param int $length Longueur
     * @return string Token
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Générer un token CSRF
     *
     * @return string Token CSRF
     */
    public static function generateCsrfToken(): string
    {
        $token = self::generateToken(32);
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    /**
     * Valider un token CSRF
     *
     * @param string $token Token à valider
     * @param int $maxAge Âge maximum en secondes
     * @return bool Valide
     */
    public static function validateCsrfToken(string $token, int $maxAge = 3600): bool
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Vérifier l'âge du token
        if (time() - $_SESSION['csrf_token_time'] > $maxAge) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        // Comparaison timing-safe
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Valider la force d'un mot de passe
     *
     * @param string $password Mot de passe
     * @param array $requirements Exigences
     * @return array [valid, errors]
     */
    public static function validatePasswordStrength(string $password, array $requirements = []): array
    {
        $errors = [];
        $defaults = [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_number' => true,
            'require_special' => true
        ];

        $requirements = array_merge($defaults, $requirements);

        if (strlen($password) < $requirements['min_length']) {
            $errors[] = "Le mot de passe doit contenir au moins {$requirements['min_length']} caractères";
        }

        if ($requirements['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }

        if ($requirements['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        }

        if ($requirements['require_number'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }

        if ($requirements['require_special'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Protéger contre les attaques timing
     *
     * @param string $known Valeur connue
     * @param string $user Valeur utilisateur
     * @return bool Égal
     */
    public static function timingSafeCompare(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }

    /**
     * Détecter les patterns d'injection SQL
     *
     * @param string $value Valeur
     * @return bool Contient pattern suspect
     */
    public static function detectSqlInjection(string $value): bool
    {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(--|#|\/\*|\*\/|;)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Détecter les patterns XSS
     *
     * @param string $value Valeur
     * @return bool Contient pattern suspect
     */
    public static function detectXss(string $value): bool
    {
        $patterns = [
            '/<script[\s\S]*?>[\s\S]*?<\/script>/i',
            '/<iframe[\s\S]*?>[\s\S]*?<\/iframe>/i',
            '/javascript:/i',
            '/on\w+\s*=/i', // onclick, onload, etc.
            '/<embed[\s\S]*?>/i',
            '/<object[\s\S]*?>/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Générer un hash pour intégrité de fichier
     *
     * @param string $filepath Chemin du fichier
     * @param string $algo Algorithme (sha256, sha512, md5)
     * @return string Hash
     */
    public static function fileHash(string $filepath, string $algo = 'sha256'): string
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("Fichier introuvable: {$filepath}");
        }

        return hash_file($algo, $filepath);
    }

    /**
     * Vérifier l'intégrité d'un fichier
     *
     * @param string $filepath Chemin
     * @param string $expectedHash Hash attendu
     * @param string $algo Algorithme
     * @return bool Intègre
     */
    public static function verifyFileIntegrity(string $filepath, string $expectedHash, string $algo = 'sha256'): bool
    {
        $actualHash = self::fileHash($filepath, $algo);
        return hash_equals($expectedHash, $actualHash);
    }

    /**
     * Encoder en base64 URL-safe
     *
     * @param string $data Données
     * @return string Encodé
     */
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Décoder base64 URL-safe
     *
     * @param string $data Données encodées
     * @return string|false Décodé
     */
    public static function base64UrlDecode(string $data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Chiffrer des données
     *
     * @param string $data Données
     * @param string $key Clé
     * @return string Données chiffrées
     */
    public static function encrypt(string $data, string $key): string
    {
        $method = 'AES-256-CBC';
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Déchiffrer des données
     *
     * @param string $data Données chiffrées
     * @param string $key Clé
     * @return string|false Données déchiffrées
     */
    public static function decrypt(string $data, string $key)
    {
        $method = 'AES-256-CBC';
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Nettoyer un tableau récursivement
     *
     * @param array $data Données
     * @return array Données nettoyées
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $key = self::escapeHtml($key);

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = self::sanitizeString($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Générer une signature HMAC
     *
     * @param string $data Données
     * @param string $key Clé secrète
     * @param string $algo Algorithme
     * @return string Signature
     */
    public static function hmacSignature(string $data, string $key, string $algo = 'sha256'): string
    {
        return hash_hmac($algo, $data, $key);
    }

    /**
     * Vérifier une signature HMAC
     *
     * @param string $data Données
     * @param string $signature Signature
     * @param string $key Clé
     * @param string $algo Algorithme
     * @return bool Valide
     */
    public static function verifyHmacSignature(string $data, string $signature, string $key, string $algo = 'sha256'): bool
    {
        $expected = self::hmacSignature($data, $key, $algo);
        return hash_equals($expected, $signature);
    }

    /**
     * Rate limiting basique par IP
     *
     * @param string $identifier Identifiant (IP, user_id)
     * @param int $maxAttempts Maximum de tentatives
     * @param int $decayMinutes Fenêtre temporelle
     * @return bool Autorisé
     */
    public static function checkRateLimit(string $identifier, int $maxAttempts, int $decayMinutes): bool
    {
        $key = 'ratelimit:' . $identifier;
        $attempts = $_SESSION[$key]['attempts'] ?? 0;
        $resetAt = $_SESSION[$key]['reset_at'] ?? 0;

        if (time() >= $resetAt) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'reset_at' => time() + ($decayMinutes * 60)
            ];
            return true;
        }

        if ($attempts < $maxAttempts) {
            $_SESSION[$key]['attempts']++;
            return true;
        }

        return false;
    }
}
