<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Année Académique
 * 
 * Valide les données de création/modification d'année académique.
 */
class AnneeAcademiqueValidator extends BaseValidator
{
    /**
     * Valide les données année académique
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Libellé obligatoire (format: 2024-2025)
        $this->validateRequired('lib_annee_acad', 'Le libellé de l\'année académique est obligatoire');
        
        if (!$this->isEmpty('lib_annee_acad')) {
            // Valider le format (AAAA-AAAA)
            if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', (string) $this->data['lib_annee_acad'])) {
                $this->addError('lib_annee_acad', 'Format invalide (ex: 2024-2025)');
            }
        }

        // Date de début obligatoire
        $this->validateRequired('date_debut', 'La date de début est obligatoire');
        $this->validateDate('date_debut', 'Format de date de début invalide (AAAA-MM-JJ)');

        // Date de fin obligatoire
        $this->validateRequired('date_fin', 'La date de fin est obligatoire');
        $this->validateDate('date_fin', 'Format de date de fin invalide (AAAA-MM-JJ)');

        // Vérifier que date_fin > date_debut
        if (!$this->isEmpty('date_debut') && !$this->isEmpty('date_fin')) {
            $debut = \DateTime::createFromFormat('Y-m-d', (string) $this->data['date_debut']);
            $fin = \DateTime::createFromFormat('Y-m-d', (string) $this->data['date_fin']);
            
            if ($debut && $fin && $fin <= $debut) {
                $this->addError('date_fin', 'La date de fin doit être postérieure à la date de début');
            }
        }

        return !$this->hasErrors();
    }
}
