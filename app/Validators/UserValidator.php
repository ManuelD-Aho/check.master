<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Utilisateur
 * 
 * Valide les données de création/modification d'utilisateur.
 */
class UserValidator
{
    private array $errors = [];

    /**
     * Valide les données utilisateur
     */
    public function validate(array $data, bool $creation = true): bool
    {
        $this->errors = [];

        // Email obligatoire
        if (empty($data['email'])) {
            $this->errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Format d\'email invalide';
        }

        // Nom obligatoire
        if (empty($data['nom_utilisateur'])) {
            $this->errors['nom_utilisateur'] = 'Le nom est obligatoire';
        } elseif (strlen($data['nom_utilisateur']) < 2) {
            $this->errors['nom_utilisateur'] = 'Le nom doit contenir au moins 2 caractères';
        }

        // Mot de passe (obligatoire en création)
        if ($creation) {
            if (empty($data['password'])) {
                $this->errors['password'] = 'Le mot de passe est obligatoire';
            } else {
                $this->validatePassword($data['password']);
            }
        } elseif (!empty($data['password'])) {
            $this->validatePassword($data['password']);
        }

        // Groupe obligatoire
        if (empty($data['groupe_id'])) {
            $this->errors['groupe_id'] = 'Le groupe est obligatoire';
        }

        return empty($this->errors);
    }

    /**
     * Valide un mot de passe
     */
    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            $this->errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
            return;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors['password'] = 'Le mot de passe doit contenir au moins une majuscule';
            return;
        }

        if (!preg_match('/[a-z]/', $password)) {
            $this->errors['password'] = 'Le mot de passe doit contenir au moins une minuscule';
            return;
        }

        if (!preg_match('/[0-9]/', $password)) {
            $this->errors['password'] = 'Le mot de passe doit contenir au moins un chiffre';
            return;
        }
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
}
