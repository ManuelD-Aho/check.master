<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Mot de Passe
 * 
 * Valide la force et la conformité d'un mot de passe.
 */
class PasswordValidator
{
    private array $errors = [];
    private int $minLength;
    private bool $requireUppercase;
    private bool $requireLowercase;
    private bool $requireNumber;
    private bool $requireSpecial;

    public function __construct(
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumber = true,
        bool $requireSpecial = false
    ) {
        $this->minLength = $minLength;
        $this->requireUppercase = $requireUppercase;
        $this->requireLowercase = $requireLowercase;
        $this->requireNumber = $requireNumber;
        $this->requireSpecial = $requireSpecial;
    }

    /**
     * Valide un mot de passe
     */
    public function validate(string $password): bool
    {
        $this->errors = [];

        if (strlen($password) < $this->minLength) {
            $this->errors[] = "Le mot de passe doit contenir au moins {$this->minLength} caractères";
        }

        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }

        if ($this->requireLowercase && !preg_match('/[a-z]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }

        if ($this->requireNumber && !preg_match('/[0-9]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }

        if ($this->requireSpecial && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
        }

        return empty($this->errors);
    }

    /**
     * Valide un changement de mot de passe
     */
    public function validateChange(
        string $currentPassword,
        string $newPassword,
        string $confirmPassword,
        string $hashedCurrent
    ): bool {
        $this->errors = [];

        // Vérifier le mot de passe actuel
        if (!password_verify($currentPassword, $hashedCurrent)) {
            $this->errors['current'] = 'Le mot de passe actuel est incorrect';
            return false;
        }

        // Vérifier que le nouveau est différent
        if ($currentPassword === $newPassword) {
            $this->errors['new'] = 'Le nouveau mot de passe doit être différent de l\'actuel';
            return false;
        }

        // Valider le nouveau mot de passe
        if (!$this->validate($newPassword)) {
            $this->errors['new'] = $this->getFirstError();
            return false;
        }

        // Vérifier la confirmation
        if ($newPassword !== $confirmPassword) {
            $this->errors['confirm'] = 'Les mots de passe ne correspondent pas';
            return false;
        }

        return true;
    }

    /**
     * Calcule le score de force (0-100)
     */
    public function getStrengthScore(string $password): int
    {
        $score = 0;

        // Longueur
        $score += min(30, strlen($password) * 3);

        // Majuscules
        if (preg_match('/[A-Z]/', $password)) {
            $score += 15;
        }

        // Minuscules
        if (preg_match('/[a-z]/', $password)) {
            $score += 15;
        }

        // Chiffres
        if (preg_match('/[0-9]/', $password)) {
            $score += 20;
        }

        // Caractères spéciaux
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $score += 20;
        }

        return min(100, $score);
    }

    /**
     * Retourne le niveau de force
     */
    public function getStrengthLevel(string $password): string
    {
        $score = $this->getStrengthScore($password);

        return match (true) {
            $score >= 80 => 'fort',
            $score >= 60 => 'moyen',
            $score >= 40 => 'faible',
            default => 'très_faible',
        };
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }

    /**
     * Alias pour validate() - pour compatibilité avec les tests
     */
    public function valider(string $password): bool
    {
        return $this->validate($password);
    }

    /**
     * Retourne les erreurs - alias pour compatibilité
     */
    public function getErreurs(): array
    {
        return $this->getErrors();
    }

    /**
     * Valide que deux mots de passe correspondent
     */
    public function validerConfirmation(string $password, string $confirmation): bool
    {
        if ($password !== $confirmation) {
            $this->errors[] = 'Les mots de passe ne correspondent pas';
            return false;
        }
        return true;
    }

    /**
     * Vérifie si un mot de passe est dans la liste des mots de passe communs
     */
    public function estMotDePasseCommun(string $password): bool
    {
        $commonPasswords = [
            'password', 'password123', '12345678', 'qwerty', 'abc123',
            'monkey', '1234567', 'letmein', 'trustno1', 'dragon',
            'baseball', 'iloveyou', 'master', 'sunshine', 'ashley',
            'bailey', 'passw0rd', 'shadow', '123123', '654321',
            'p@ssw0rd'
        ];

        return in_array(strtolower($password), $commonPasswords, true);
    }

    /**
     * Calcule la force du mot de passe - alias pour compatibilité
     */
    public function calculerForce(string $password): int
    {
        return $this->getStrengthScore($password);
    }

    /**
     * Formate les erreurs en une chaîne
     */
    public function getErreursFormatees(): string
    {
        return implode(', ', $this->errors);
    }
}
