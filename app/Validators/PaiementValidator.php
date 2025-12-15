<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Paiement
 * 
 * Valide les données d'enregistrement de paiement.
 */
class PaiementValidator
{
    private array $errors = [];

    /**
     * Modes de paiement valides
     */
    private const MODES_VALIDES = ['Especes', 'Cheque', 'Virement', 'Mobile_Money'];

    /**
     * Valide les données de paiement
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // Étudiant obligatoire
        if (empty($data['etudiant_id'])) {
            $this->errors['etudiant_id'] = 'L\'étudiant est obligatoire';
        }

        // Montant obligatoire et positif
        if (!isset($data['montant'])) {
            $this->errors['montant'] = 'Le montant est obligatoire';
        } elseif (!is_numeric($data['montant'])) {
            $this->errors['montant'] = 'Le montant doit être un nombre';
        } elseif ((float) $data['montant'] <= 0) {
            $this->errors['montant'] = 'Le montant doit être positif';
        }

        // Mode de paiement obligatoire et valide
        if (empty($data['mode_paiement'])) {
            $this->errors['mode_paiement'] = 'Le mode de paiement est obligatoire';
        } elseif (!in_array($data['mode_paiement'], self::MODES_VALIDES)) {
            $this->errors['mode_paiement'] = 'Mode de paiement invalide';
        }

        // Année académique obligatoire
        if (empty($data['annee_acad_id'])) {
            $this->errors['annee_acad_id'] = 'L\'année académique est obligatoire';
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
