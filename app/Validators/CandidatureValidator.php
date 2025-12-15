<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Candidature
 * 
 * Valide les données de soumission de candidature.
 */
class CandidatureValidator
{
    private array $errors = [];

    /**
     * Valide les données de candidature
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // Thème obligatoire
        if (empty($data['theme'])) {
            $this->errors['theme'] = 'Le thème est obligatoire';
        } elseif (strlen($data['theme']) < 20) {
            $this->errors['theme'] = 'Le thème doit contenir au moins 20 caractères';
        } elseif (strlen($data['theme']) > 500) {
            $this->errors['theme'] = 'Le thème ne doit pas dépasser 500 caractères';
        }

        // Entreprise obligatoire
        if (empty($data['entreprise_id'])) {
            $this->errors['entreprise_id'] = 'L\'entreprise est obligatoire';
        }

        // Maître de stage
        if (empty($data['maitre_stage_nom'])) {
            $this->errors['maitre_stage_nom'] = 'Le nom du maître de stage est obligatoire';
        }

        if (empty($data['maitre_stage_email'])) {
            $this->errors['maitre_stage_email'] = 'L\'email du maître de stage est obligatoire';
        } elseif (!filter_var($data['maitre_stage_email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['maitre_stage_email'] = 'Format d\'email invalide';
        }

        if (!empty($data['maitre_stage_tel']) && !preg_match('/^(\+225)?[0-9]{10}$/', $data['maitre_stage_tel'])) {
            $this->errors['maitre_stage_tel'] = 'Format de téléphone invalide';
        }

        // Dates de stage
        if (empty($data['date_debut_stage'])) {
            $this->errors['date_debut_stage'] = 'La date de début de stage est obligatoire';
        }

        if (empty($data['date_fin_stage'])) {
            $this->errors['date_fin_stage'] = 'La date de fin de stage est obligatoire';
        }

        // Vérifier cohérence des dates
        if (!empty($data['date_debut_stage']) && !empty($data['date_fin_stage'])) {
            $debut = \DateTime::createFromFormat('Y-m-d', $data['date_debut_stage']);
            $fin = \DateTime::createFromFormat('Y-m-d', $data['date_fin_stage']);

            if ($debut && $fin && $fin <= $debut) {
                $this->errors['date_fin_stage'] = 'La date de fin doit être postérieure à la date de début';
            }
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
