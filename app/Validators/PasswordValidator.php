<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur pour les mots de passe
 * 
 * Vérifie la politique de mots de passe:
 * - Minimum 8 caractères
 * - Au moins 1 majuscule
 * - Au moins 1 chiffre
 * - Au moins 1 caractère spécial
 */
class PasswordValidator
{
    private const MIN_LENGTH = 8;
    private array $erreurs = [];

    /**
     * Valide un mot de passe selon la politique de sécurité
     */
    public function valider(string $password): bool
    {
        $this->erreurs = [];

        // Vérifier la longueur minimale
        if (strlen($password) < self::MIN_LENGTH) {
            $this->erreurs[] = 'Le mot de passe doit contenir au moins ' . self::MIN_LENGTH . ' caractères.';
        }

        // Vérifier la présence d'une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            $this->erreurs[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }

        // Vérifier la présence d'un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            $this->erreurs[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }

        // Vérifier la présence d'un caractère spécial
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $this->erreurs[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }

        return empty($this->erreurs);
    }

    /**
     * Valide la confirmation du mot de passe
     */
    public function validerConfirmation(string $password, string $confirmation): bool
    {
        if ($password !== $confirmation) {
            $this->erreurs[] = 'Les mots de passe ne correspondent pas.';
            return false;
        }
        return true;
    }

    /**
     * Retourne toutes les erreurs
     */
    public function getErreurs(): array
    {
        return $this->erreurs;
    }

    /**
     * Retourne les erreurs formatées en chaîne
     */
    public function getErreursFormatees(): string
    {
        return implode(' ', $this->erreurs);
    }

    /**
     * Vérifie si le mot de passe est trop commun
     */
    public function estMotDePasseCommun(string $password): bool
    {
        $motsDePasseCommuns = [
            'password',
            'Password1!',
            '12345678',
            'azerty123',
            'qwerty123',
            'admin123',
            'Letmein1!',
            'Welcome1!',
            'Changeme1!',
            'P@ssw0rd',
        ];

        return in_array($password, $motsDePasseCommuns, true);
    }

    /**
     * Calcule la force du mot de passe (0-100)
     */
    public function calculerForce(string $password): int
    {
        $force = 0;

        // Points pour la longueur
        $longueur = strlen($password);
        $force += min(30, $longueur * 3);

        // Points pour les majuscules
        if (preg_match('/[A-Z]/', $password)) {
            $force += 15;
        }

        // Points pour les minuscules
        if (preg_match('/[a-z]/', $password)) {
            $force += 10;
        }

        // Points pour les chiffres
        if (preg_match('/[0-9]/', $password)) {
            $force += 15;
        }

        // Points pour les caractères spéciaux
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $force += 20;
        }

        // Points bonus pour diversité
        if (
            $longueur >= 12 && preg_match('/[A-Z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            $force += 10;
        }

        return min(100, $force);
    }
}
