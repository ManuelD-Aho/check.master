<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur UE (Unité d'Enseignement)
 * 
 * Valide les données de création/modification d'UE.
 */
class UeValidator extends BaseValidator
{
    /**
     * Valide les données UE
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Code UE obligatoire
        $this->validateRequired('code_ue', 'Le code UE est obligatoire');
        $this->validateMinLength('code_ue', 2, 'Le code UE doit contenir au moins 2 caractères');
        $this->validateMaxLength('code_ue', 20, 'Le code UE ne doit pas dépasser 20 caractères');
        
        // Format alphanumeric avec tirets/underscores
        if (!$this->isEmpty('code_ue')) {
            if (!preg_match('/^[A-Za-z0-9_-]+$/', (string) $this->data['code_ue'])) {
                $this->addError('code_ue', 'Le code UE ne doit contenir que des lettres, chiffres, tirets et underscores');
            }
        }

        // Libellé obligatoire
        $this->validateRequired('lib_ue', 'Le libellé UE est obligatoire');
        $this->validateMinLength('lib_ue', 3, 'Le libellé doit contenir au moins 3 caractères');
        $this->validateMaxLength('lib_ue', 255, 'Le libellé ne doit pas dépasser 255 caractères');

        // Crédits (si fournis, positif)
        if (!$this->isEmpty('credits')) {
            $this->validatePositiveInteger('credits', 'Les crédits doivent être un nombre positif');
            
            // Crédits raisonnables (1-30)
            if (isset($this->data['credits']) && ((int) $this->data['credits'] > 30)) {
                $this->addError('credits', 'Les crédits doivent être entre 1 et 30');
            }
        }

        // Niveau (si fourni, positif)
        if (!$this->isEmpty('niveau_id')) {
            $this->validatePositiveInteger('niveau_id', 'Niveau invalide');
        }

        // Semestre (si fourni, positif)
        if (!$this->isEmpty('semestre_id')) {
            $this->validatePositiveInteger('semestre_id', 'Semestre invalide');
        }

        return !$this->hasErrors();
    }
}
