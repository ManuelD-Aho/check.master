<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur pour les données de connexion
 * 
 * Valide l'email et le mot de passe avant tentative d'authentification.
 */
class LoginValidator
{
    private array $erreurs = [];

    /**
     * Valide les données de connexion
     *
     * @param array $data ['email' => ..., 'password' => ...]
     * @return bool True si valide
     */
    public function valider(array $data): bool
    {
        $this->erreurs = [];

        // Valider l'email
        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $this->erreurs['email'] = 'L\'adresse email est requise.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->erreurs['email'] = 'L\'adresse email n\'est pas valide.';
        }

        // Valider le mot de passe
        $password = $data['password'] ?? '';
        if ($password === '') {
            $this->erreurs['password'] = 'Le mot de passe est requis.';
        }

        return empty($this->erreurs);
    }

    /**
     * Retourne les erreurs de validation
     */
    public function getErreurs(): array
    {
        return $this->erreurs;
    }

    /**
     * Retourne la première erreur
     */
    public function getPremiereErreur(): ?string
    {
        return reset($this->erreurs) ?: null;
    }

    /**
     * Vérifie si un champ a une erreur
     */
    public function aErreur(string $champ): bool
    {
        return isset($this->erreurs[$champ]);
    }

    /**
     * Retourne l'erreur pour un champ
     */
    public function getErreur(string $champ): ?string
    {
        return $this->erreurs[$champ] ?? null;
    }
}
