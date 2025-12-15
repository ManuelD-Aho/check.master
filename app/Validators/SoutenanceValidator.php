<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Soutenance
 * 
 * Valide les données de planification de soutenance.
 */
class SoutenanceValidator
{
    private array $errors = [];

    /**
     * Valide les données de soutenance
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // Dossier obligatoire
        if (empty($data['dossier_id'])) {
            $this->errors['dossier_id'] = 'Le dossier est obligatoire';
        }

        // Date obligatoire et valide
        if (empty($data['date_soutenance'])) {
            $this->errors['date_soutenance'] = 'La date de soutenance est obligatoire';
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $data['date_soutenance']);
            if (!$date) {
                $this->errors['date_soutenance'] = 'Format de date invalide';
            } elseif ($date < new \DateTime('today')) {
                $this->errors['date_soutenance'] = 'La date ne peut pas être dans le passé';
            }
        }

        // Heure de début obligatoire
        if (empty($data['heure_debut'])) {
            $this->errors['heure_debut'] = 'L\'heure de début est obligatoire';
        } elseif (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['heure_debut'])) {
            $this->errors['heure_debut'] = 'Format d\'heure invalide (HH:MM)';
        }

        // Heure de fin obligatoire
        if (empty($data['heure_fin'])) {
            $this->errors['heure_fin'] = 'L\'heure de fin est obligatoire';
        } elseif (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['heure_fin'])) {
            $this->errors['heure_fin'] = 'Format d\'heure invalide (HH:MM)';
        }

        // Cohérence des heures
        if (!empty($data['heure_debut']) && !empty($data['heure_fin'])) {
            if ($data['heure_fin'] <= $data['heure_debut']) {
                $this->errors['heure_fin'] = 'L\'heure de fin doit être après l\'heure de début';
            }
        }

        // Salle obligatoire
        if (empty($data['salle_id'])) {
            $this->errors['salle_id'] = 'La salle est obligatoire';
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
