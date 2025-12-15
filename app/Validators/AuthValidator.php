<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur pour l'authentification
 */
class AuthValidator
{
    private array $errors = [];

    public function validateLogin(array $data): bool
    {
        $this->errors = [];

        if (empty($data['login'])) {
            $this->errors['login'] = "Le login est requis";
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Le mot de passe est requis";
        }

        return empty($this->errors);
    }

    public function validateRegister(array $data): bool
    {
        $this->errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email invalide";
        }

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $this->errors['password'] = "Le mot de passe doit faire au moins 8 caractères";
        }

        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $this->errors['confirm_password'] = "Les mots de passe ne correspondent pas";
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
