<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Étudiant
 * 
 * Valide les données de création/modification d'étudiant.
 */
class EtudiantValidator
{
    private array $errors = [];

    /**
     * Valide les données étudiant
     */
    public function validate(array $data, bool $creation = true): bool
    {
        $this->errors = [];

        // Numéro étudiant obligatoire en création
        if ($creation && empty($data['num_etu'])) {
            $this->errors['num_etu'] = 'Le numéro étudiant est obligatoire';
        } elseif (!empty($data['num_etu']) && !preg_match('/^[A-Z]{2}[0-9]{8}$/', $data['num_etu'])) {
            $this->errors['num_etu'] = 'Format de numéro étudiant invalide (ex: AB12345678)';
        }

        // Nom obligatoire
        if (empty($data['nom_etu'])) {
            $this->errors['nom_etu'] = 'Le nom est obligatoire';
        } elseif (strlen($data['nom_etu']) < 2) {
            $this->errors['nom_etu'] = 'Le nom doit contenir au moins 2 caractères';
        }

        // Prénom obligatoire
        if (empty($data['prenom_etu'])) {
            $this->errors['prenom_etu'] = 'Le prénom est obligatoire';
        }

        // Email facultatif mais valide
        if (!empty($data['email_etu']) && !filter_var($data['email_etu'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email_etu'] = 'Format d\'email invalide';
        }

        // Téléphone facultatif mais valide
        if (!empty($data['telephone_etu']) && !preg_match('/^(\+225)?[0-9]{10}$/', $data['telephone_etu'])) {
            $this->errors['telephone_etu'] = 'Format de téléphone invalide';
        }

        // Date de naissance valide
        if (!empty($data['date_naiss_etu'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['date_naiss_etu']);
            if (!$date) {
                $this->errors['date_naiss_etu'] = 'Format de date invalide (AAAA-MM-JJ)';
            } elseif ($date > new \DateTime()) {
                $this->errors['date_naiss_etu'] = 'La date de naissance ne peut pas être dans le futur';
            }
        }

        // Genre valide
        if (!empty($data['genre_etu']) && !in_array($data['genre_etu'], ['Homme', 'Femme', 'Autre'])) {
            $this->errors['genre_etu'] = 'Genre invalide';
        }

        return empty($this->errors);
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
