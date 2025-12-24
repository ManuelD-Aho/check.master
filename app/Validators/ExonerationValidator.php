<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Exonération
 * 
 * Valide les données des exonérations de frais.
 */
class ExonerationValidator extends BaseValidator
{
    /**
     * Types d'exonération valides
     */
    private const TYPES_EXONERATION = [
        'totale',
        'partielle',
        'echelonnement',
    ];

    /**
     * Motifs d'exonération valides
     */
    private const MOTIFS_EXONERATION = [
        'boursier',
        'merite',
        'handicap',
        'situation_sociale',
        'personnel_ufr',
        'accord_partenariat',
        'autre',
    ];

    /**
     * Statuts de demande valides
     */
    private const STATUTS_DEMANDE = [
        'en_attente',
        'approuvee',
        'refusee',
        'annulee',
    ];

    /**
     * Pourcentage maximum d'exonération partielle
     */
    private const MAX_POURCENTAGE = 100;

    /**
     * Nombre maximum d'échéances
     */
    private const MAX_ECHEANCES = 12;

    /**
     * Valide les données d'exonération
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Étudiant obligatoire
        $this->validateRequired('etudiant_id', 'L\'étudiant est obligatoire');
        $this->validatePositiveInteger('etudiant_id');

        // Type d'exonération obligatoire
        $this->validateRequired('type', 'Le type d\'exonération est obligatoire');
        $this->validateInArray('type', self::TYPES_EXONERATION, 'Type d\'exonération invalide');

        // Motif obligatoire
        $this->validateRequired('motif', 'Le motif est obligatoire');
        $this->validateInArray('motif', self::MOTIFS_EXONERATION, 'Motif d\'exonération invalide');

        // Validation spécifique selon le type
        if (!$this->isEmpty('type')) {
            switch ($this->data['type']) {
                case 'partielle':
                    $this->validateExonerationPartielle();
                    break;
                case 'echelonnement':
                    $this->validateEchelonnement();
                    break;
            }
        }

        // Justification obligatoire
        $this->validateRequired('justification', 'La justification est obligatoire');
        $this->validateMinLength('justification', 20, 'La justification doit contenir au moins 20 caractères');
        $this->validateMaxLength('justification', 2000);

        // Année académique
        if (!$this->isEmpty('annee_academique')) {
            $this->validateRegex('annee_academique', '/^\d{4}-\d{4}$/', 'Format d\'année académique invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une exonération partielle
     */
    private function validateExonerationPartielle(): void
    {
        // Pourcentage obligatoire pour exonération partielle
        $this->validateRequired('pourcentage', 'Le pourcentage d\'exonération est obligatoire');
        $this->validatePositiveInteger('pourcentage');

        if (!$this->isEmpty('pourcentage')) {
            $pourcentage = (int) $this->data['pourcentage'];
            if ($pourcentage < 1 || $pourcentage > self::MAX_POURCENTAGE) {
                $this->addError('pourcentage', 'Le pourcentage doit être entre 1 et 100');
            }

            // Si 100%, c'est une exonération totale
            if ($pourcentage === 100) {
                $this->addError('type', 'Pour une exonération de 100%, utilisez le type "totale"');
            }
        }
    }

    /**
     * Valide un échelonnement de paiement
     */
    private function validateEchelonnement(): void
    {
        // Nombre d'échéances obligatoire
        $this->validateRequired('nombre_echeances', 'Le nombre d\'échéances est obligatoire');
        $this->validatePositiveInteger('nombre_echeances');

        if (!$this->isEmpty('nombre_echeances')) {
            $echeances = (int) $this->data['nombre_echeances'];
            if ($echeances < 2 || $echeances > self::MAX_ECHEANCES) {
                $this->addError('nombre_echeances', 'Le nombre d\'échéances doit être entre 2 et ' . self::MAX_ECHEANCES);
            }
        }

        // Date de première échéance
        if (!$this->isEmpty('date_premiere_echeance')) {
            $this->validateDate('date_premiere_echeance');
            $this->validateFutureDate('date_premiere_echeance', 'La date de première échéance doit être dans le futur');
        }
    }

    /**
     * Valide une demande d'exonération
     *
     * @param array<string, mixed> $data
     */
    public function validateDemande(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Type obligatoire
        $this->validateRequired('type', 'Le type d\'exonération demandée est obligatoire');
        $this->validateInArray('type', self::TYPES_EXONERATION);

        // Motif obligatoire
        $this->validateRequired('motif', 'Le motif est obligatoire');
        $this->validateInArray('motif', self::MOTIFS_EXONERATION);

        // Justification détaillée obligatoire
        $this->validateRequired('justification', 'Une justification détaillée est obligatoire');
        $this->validateMinLength('justification', 50, 'La justification doit contenir au moins 50 caractères');
        $this->validateMaxLength('justification', 5000);

        // Pièces justificatives
        if (!$this->isEmpty('pieces_justificatives')) {
            $this->validateArray('pieces_justificatives');
        }

        // Si motif "autre", détail obligatoire
        if (!$this->isEmpty('motif') && $this->data['motif'] === 'autre') {
            $this->validateRequired('motif_detail', 'Veuillez préciser le motif');
            $this->validateMinLength('motif_detail', 10);
            $this->validateMaxLength('motif_detail', 500);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide la décision sur une demande d'exonération
     *
     * @param array<string, mixed> $data
     */
    public function validateDecision(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Demande obligatoire
        $this->validateRequired('demande_id', 'La demande est obligatoire');
        $this->validatePositiveInteger('demande_id');

        // Décision obligatoire
        $this->validateRequired('decision', 'La décision est obligatoire');
        $this->validateInArray('decision', ['approuvee', 'refusee'], 'Décision invalide');

        // Commentaire obligatoire
        $this->validateRequired('commentaire', 'Un commentaire est obligatoire');
        $this->validateMinLength('commentaire', 10);
        $this->validateMaxLength('commentaire', 2000);

        // Si approbation partielle, valider les détails
        if (!$this->isEmpty('decision') && $this->data['decision'] === 'approuvee') {
            if (!$this->isEmpty('pourcentage_accorde')) {
                $this->validatePositiveInteger('pourcentage_accorde');
                $pourcentage = (int) $this->data['pourcentage_accorde'];
                if ($pourcentage < 1 || $pourcentage > 100) {
                    $this->addError('pourcentage_accorde', 'Le pourcentage doit être entre 1 et 100');
                }
            }
        }

        // Si refus, motif de refus obligatoire
        if (!$this->isEmpty('decision') && $this->data['decision'] === 'refusee') {
            $this->validateRequired('motif_refus', 'Le motif de refus est obligatoire');
            $this->validateMinLength('motif_refus', 20);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide l'annulation d'une exonération
     *
     * @param array<string, mixed> $data
     */
    public function validateAnnulation(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Exonération obligatoire
        $this->validateRequired('exoneration_id', 'L\'exonération est obligatoire');
        $this->validatePositiveInteger('exoneration_id');

        // Motif d'annulation obligatoire
        $this->validateRequired('motif_annulation', 'Le motif d\'annulation est obligatoire');
        $this->validateMinLength('motif_annulation', 20);
        $this->validateMaxLength('motif_annulation', 1000);

        return !$this->hasErrors();
    }
}
