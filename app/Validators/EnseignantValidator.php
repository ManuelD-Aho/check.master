<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Enseignant
 * 
 * Valide les données de création/modification d'enseignant.
 */
class EnseignantValidator extends BaseValidator
{
    /**
     * Valide les données enseignant
     */
    public function validate(array $data, bool $creation = true): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nom obligatoire
        $this->validateRequired('nom_ens', 'Le nom est obligatoire');
        $this->validateMinLength('nom_ens', 2, 'Le nom doit contenir au moins 2 caractères');
        $this->validateMaxLength('nom_ens', 100, 'Le nom ne doit pas dépasser 100 caractères');

        // Prénom obligatoire
        $this->validateRequired('prenom_ens', 'Le prénom est obligatoire');
        $this->validateMinLength('prenom_ens', 2, 'Le prénom doit contenir au moins 2 caractères');
        $this->validateMaxLength('prenom_ens', 100, 'Le prénom ne doit pas dépasser 100 caractères');

        // Email obligatoire et valide
        if ($creation) {
            $this->validateRequired('email_ens', 'L\'email est obligatoire');
        }
        $this->validateEmail('email_ens', 'Format d\'email invalide');
        $this->validateMaxLength('email_ens', 255, 'L\'email ne doit pas dépasser 255 caractères');

        // Téléphone facultatif mais valide
        $this->validatePhone('telephone_ens', 'Format de téléphone invalide');

        // Grade valide si fourni
        if (!$this->isEmpty('grade_id')) {
            $this->validatePositiveInteger('grade_id', 'Grade invalide');
        }

        // Fonction valide si fournie
        if (!$this->isEmpty('fonction_id')) {
            $this->validatePositiveInteger('fonction_id', 'Fonction invalide');
        }

        // Spécialité valide si fournie
        if (!$this->isEmpty('specialite_id')) {
            $this->validatePositiveInteger('specialite_id', 'Spécialité invalide');
        }

        return !$this->hasErrors();
    }
}
