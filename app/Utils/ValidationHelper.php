<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour la validation
 * 
 * Utilitaires pour les règles de validation communes.
 */
class ValidationHelper
{
    /**
     * Valide un email
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide un numéro de téléphone (format Côte d'Ivoire)
     */
    public static function isValidPhone(string $phone): bool
    {
        // Format: +225XXXXXXXXXX ou 0XXXXXXXXX
        return (bool) preg_match('/^(\+225)?[0-9]{10}$/', $phone);
    }

    /**
     * Valide un numéro de carte étudiant
     */
    public static function isValidStudentCard(string $cardNumber): bool
    {
        // Format: CI01552852 (2 lettres + 8 chiffres)
        return (bool) preg_match('/^[A-Z]{2}\d{8}$/', $cardNumber);
    }

    /**
     * Valide une URL
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valide une date au format Y-m-d
     */
    public static function isValidDate(string $date): bool
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        return $dt !== false && $dt->format('Y-m-d') === $date;
    }

    /**
     * Valide une heure au format H:i
     */
    public static function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time);
    }

    /**
     * Valide une année académique (format YYYY-YYYY)
     */
    public static function isValidAcademicYear(string $year): bool
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', $year, $matches)) {
            return false;
        }

        $start = (int) $matches[1];
        $end = (int) $matches[2];

        return $end === $start + 1;
    }

    /**
     * Valide un montant (positif avec max 2 décimales)
     */
    public static function isValidAmount(mixed $amount): bool
    {
        if (!is_numeric($amount)) {
            return false;
        }

        $floatAmount = (float) $amount;
        return $floatAmount >= 0 && round($floatAmount, 2) === $floatAmount;
    }

    /**
     * Valide un pourcentage (0-100)
     */
    public static function isValidPercentage(mixed $value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $floatValue = (float) $value;
        return $floatValue >= 0 && $floatValue <= 100;
    }

    /**
     * Valide une note (0-20)
     */
    public static function isValidGrade(mixed $grade): bool
    {
        if (!is_numeric($grade)) {
            return false;
        }

        $floatGrade = (float) $grade;
        return $floatGrade >= 0 && $floatGrade <= 20;
    }

    /**
     * Valide un JSON
     */
    public static function isValidJson(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Valide un hash SHA256
     */
    public static function isValidSha256(string $hash): bool
    {
        return (bool) preg_match('/^[a-f0-9]{64}$/i', $hash);
    }

    /**
     * Valide un UUID v4
     */
    public static function isValidUuid(string $uuid): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    /**
     * Valide un mot de passe selon les règles CheckMaster
     */
    public static function isValidPassword(string $password): bool
    {
        // Minimum 8 caractères
        if (strlen($password) < 8) {
            return false;
        }

        // Au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Au moins une minuscule
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie la complexité d'un mot de passe et retourne les erreurs
     *
     * @return array<string>
     */
    public static function getPasswordErrors(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }

        return $errors;
    }

    /**
     * Valide une adresse IP
     */
    public static function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Valide une adresse IPv4
     */
    public static function isValidIpv4(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Valide un domaine email
     */
    public static function isValidEmailDomain(string $email): bool
    {
        if (!self::isValidEmail($email)) {
            return false;
        }

        $domain = substr($email, strrpos($email, '@') + 1);
        return checkdnsrr($domain, 'MX');
    }

    /**
     * Vérifie si une chaîne ne contient que des caractères alphanumériques
     */
    public static function isAlphanumeric(string $value): bool
    {
        return ctype_alnum($value);
    }

    /**
     * Vérifie si une chaîne ne contient que des lettres
     */
    public static function isAlpha(string $value): bool
    {
        return (bool) preg_match('/^[\p{L}]+$/u', $value);
    }

    /**
     * Vérifie si une valeur est dans une plage
     */
    public static function isInRange(float $value, float $min, float $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * Vérifie si une chaîne respecte une longueur
     */
    public static function hasValidLength(string $value, int $min, int $max): bool
    {
        $length = mb_strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * Vérifie si une date est dans le passé
     */
    public static function isPastDate(string $date): bool
    {
        if (!self::isValidDate($date)) {
            return false;
        }

        return strtotime($date) < strtotime('today');
    }

    /**
     * Vérifie si une date est dans le futur
     */
    public static function isFutureDate(string $date): bool
    {
        if (!self::isValidDate($date)) {
            return false;
        }

        return strtotime($date) > strtotime('today');
    }

    /**
     * Valide un code de transition workflow
     */
    public static function isValidTransitionCode(string $code): bool
    {
        return (bool) preg_match('/^[a-z][a-z0-9_]*$/', $code);
    }

    /**
     * Valide un code de template notification
     */
    public static function isValidTemplateCode(string $code): bool
    {
        return (bool) preg_match('/^[A-Z][A-Z0-9_]*$/', $code);
    }
}
