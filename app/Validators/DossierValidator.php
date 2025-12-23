<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Dossier
 * 
 * Valide les données des dossiers étudiants.
 */
class DossierValidator extends BaseValidator
{
    /**
     * États valides du dossier
     */
    private const ETATS_VALIDES = [
        'inscrit',
        'candidature_soumise',
        'verification_scolarite',
        'filtre_communication',
        'en_attente_commission',
        'en_evaluation_commission',
        'rapport_valide',
        'attente_avis_encadreur',
        'pret_pour_jury',
        'jury_en_constitution',
        'soutenance_planifiee',
        'soutenance_en_cours',
        'soutenance_terminee',
        'diplome_delivre',
    ];

    /**
     * Valide les données du dossier
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Validation de l'étudiant
        $this->validateRequired('etudiant_id', 'L\'étudiant est obligatoire');
        $this->validatePositiveInteger('etudiant_id');

        // Validation de l'année académique
        $this->validateRequired('annee_academique', 'L\'année académique est obligatoire');
        $this->validateRegex('annee_academique', '/^\d{4}-\d{4}$/', 'Format d\'année académique invalide (ex: 2024-2025)');

        // Validation de l'état
        if (!$this->isEmpty('etat_actuel')) {
            $this->validateInArray('etat_actuel', self::ETATS_VALIDES, 'État du dossier invalide');
        }

        // Validation du thème si présent
        if (!$this->isEmpty('theme')) {
            $this->validateMinLength('theme', 20, 'Le thème doit contenir au moins 20 caractères');
            $this->validateMaxLength('theme', 500, 'Le thème ne doit pas dépasser 500 caractères');
        }

        // Validation de l'entreprise si présente
        if (!$this->isEmpty('entreprise_id')) {
            $this->validatePositiveInteger('entreprise_id', 'ID entreprise invalide');
        }

        // Validation des dates de stage
        $this->validateDatesStage();

        // Validation des encadreurs
        $this->validateEncadreurs();

        return !$this->hasErrors();
    }

    /**
     * Valide la cohérence des dates de stage
     */
    private function validateDatesStage(): void
    {
        $this->validateDate('date_debut_stage');
        $this->validateDate('date_fin_stage');

        if (!$this->isEmpty('date_debut_stage') && !$this->isEmpty('date_fin_stage')) {
            $debut = \DateTime::createFromFormat('Y-m-d', (string) $this->data['date_debut_stage']);
            $fin = \DateTime::createFromFormat('Y-m-d', (string) $this->data['date_fin_stage']);

            if ($debut && $fin && $fin <= $debut) {
                $this->addError('date_fin_stage', 'La date de fin doit être postérieure à la date de début');
            }

            // Vérifier la durée minimale (3 mois)
            if ($debut && $fin) {
                $diff = $debut->diff($fin);
                if ($diff->days < 90) {
                    $this->addError('date_fin_stage', 'La durée du stage doit être d\'au moins 3 mois');
                }
            }
        }
    }

    /**
     * Valide les informations sur les encadreurs
     */
    private function validateEncadreurs(): void
    {
        if (!$this->isEmpty('directeur_id')) {
            $this->validatePositiveInteger('directeur_id', 'ID directeur invalide');
        }

        if (!$this->isEmpty('encadreur_id')) {
            $this->validatePositiveInteger('encadreur_id', 'ID encadreur invalide');
        }

        // Validation du maître de stage
        if (!$this->isEmpty('maitre_stage_nom')) {
            $this->validateMaxLength('maitre_stage_nom', 100, 'Le nom du maître de stage ne doit pas dépasser 100 caractères');
        }

        if (!$this->isEmpty('maitre_stage_email')) {
            $this->validateEmail('maitre_stage_email', 'Email du maître de stage invalide');
        }

        if (!$this->isEmpty('maitre_stage_tel')) {
            $this->validatePhone('maitre_stage_tel', 'Téléphone du maître de stage invalide');
        }
    }

    /**
     * Valide les données pour la soumission de candidature
     *
     * @param array<string, mixed> $data
     */
    public function validateCandidature(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Thème obligatoire
        $this->validateRequired('theme', 'Le thème est obligatoire');
        $this->validateMinLength('theme', 20, 'Le thème doit contenir au moins 20 caractères');
        $this->validateMaxLength('theme', 500, 'Le thème ne doit pas dépasser 500 caractères');

        // Entreprise obligatoire
        $this->validateRequired('entreprise_id', 'L\'entreprise est obligatoire');

        // Maître de stage obligatoire
        $this->validateRequired('maitre_stage_nom', 'Le nom du maître de stage est obligatoire');
        $this->validateRequired('maitre_stage_email', 'L\'email du maître de stage est obligatoire');
        $this->validateEmail('maitre_stage_email');
        $this->validatePhone('maitre_stage_tel');

        // Dates de stage obligatoires
        $this->validateRequired('date_debut_stage', 'La date de début de stage est obligatoire');
        $this->validateRequired('date_fin_stage', 'La date de fin de stage est obligatoire');
        $this->validateDatesStage();

        return !$this->hasErrors();
    }

    /**
     * Valide les données pour l'assignation des encadreurs
     *
     * @param array<string, mixed> $data
     */
    public function validateAssignationEncadreurs(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Directeur obligatoire
        $this->validateRequired('directeur_id', 'Le directeur de mémoire est obligatoire');
        $this->validatePositiveInteger('directeur_id');

        // Encadreur optionnel mais valide si présent
        if (!$this->isEmpty('encadreur_id')) {
            $this->validatePositiveInteger('encadreur_id');
        }

        // Vérifier que directeur et encadreur sont différents
        if (!$this->isEmpty('directeur_id') && !$this->isEmpty('encadreur_id')) {
            if ($this->data['directeur_id'] === $this->data['encadreur_id']) {
                $this->addError('encadreur_id', 'L\'encadreur doit être différent du directeur');
            }
        }

        return !$this->hasErrors();
    }
}
