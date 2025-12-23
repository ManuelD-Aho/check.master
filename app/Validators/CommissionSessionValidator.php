<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Session Commission
 * 
 * Valide les données des sessions de commission.
 */
class CommissionSessionValidator extends BaseValidator
{
    /**
     * Statuts de session valides
     */
    private const STATUTS_VALIDES = [
        'planifiee',
        'en_cours',
        'en_attente_votes',
        'tour_2',
        'tour_3',
        'escalade',
        'terminee',
        'annulee',
    ];

    /**
     * Types de décision valides
     */
    private const DECISIONS_VALIDES = [
        'valider',
        'a_revoir',
        'rejeter',
    ];

    /**
     * Valide les données de session
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Date de session obligatoire
        $this->validateRequired('date_session', 'La date de session est obligatoire');
        $this->validateDate('date_session');
        $this->validateFutureDate('date_session', 'La date de session doit être dans le futur');

        // Heure de début
        if (!$this->isEmpty('heure_debut')) {
            $this->validateRegex('heure_debut', '/^([01]\d|2[0-3]):([0-5]\d)$/', 'Format d\'heure invalide (HH:MM)');
        }

        // Heure de fin
        if (!$this->isEmpty('heure_fin')) {
            $this->validateRegex('heure_fin', '/^([01]\d|2[0-3]):([0-5]\d)$/', 'Format d\'heure invalide (HH:MM)');
        }

        // Vérifier cohérence heures
        $this->validateHeures();

        // Salle/lieu
        if (!$this->isEmpty('lieu')) {
            $this->validateMaxLength('lieu', 200, 'Le lieu ne doit pas dépasser 200 caractères');
        }

        // Description
        if (!$this->isEmpty('description')) {
            $this->validateMaxLength('description', 1000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide la cohérence des heures
     */
    private function validateHeures(): void
    {
        if ($this->isEmpty('heure_debut') || $this->isEmpty('heure_fin')) {
            return;
        }

        $debut = strtotime((string) $this->data['heure_debut']);
        $fin = strtotime((string) $this->data['heure_fin']);

        if ($debut !== false && $fin !== false && $fin <= $debut) {
            $this->addError('heure_fin', 'L\'heure de fin doit être postérieure à l\'heure de début');
        }
    }

    /**
     * Valide l'ajout de membres à la session
     *
     * @param array<string, mixed> $data
     */
    public function validateMembres(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Liste des membres obligatoire
        $this->validateRequired('membres', 'La liste des membres est obligatoire');
        
        if (!$this->isEmpty('membres')) {
            $this->validateArray('membres');
            $this->validateArrayMin('membres', 3, 'Il faut au moins 3 membres pour une session');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide l'ajout de rapports à la session
     *
     * @param array<string, mixed> $data
     */
    public function validateRapports(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Liste des rapports obligatoire
        $this->validateRequired('rapports', 'La liste des rapports est obligatoire');
        
        if (!$this->isEmpty('rapports')) {
            $this->validateArray('rapports');
            $this->validateArrayMin('rapports', 1, 'Il faut au moins 1 rapport à évaluer');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide un vote
     *
     * @param array<string, mixed> $data
     */
    public function validateVote(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Rapport obligatoire
        $this->validateRequired('rapport_id', 'Le rapport est obligatoire');
        $this->validatePositiveInteger('rapport_id');

        // Décision obligatoire
        $this->validateRequired('decision', 'La décision est obligatoire');
        $this->validateInArray('decision', self::DECISIONS_VALIDES, 'Décision invalide');

        // Commentaire obligatoire si rejet ou à revoir
        if (!$this->isEmpty('decision') && in_array($this->data['decision'], ['a_revoir', 'rejeter'], true)) {
            $this->validateRequired('commentaire', 'Un commentaire est obligatoire pour cette décision');
            $this->validateMinLength('commentaire', 20, 'Le commentaire doit contenir au moins 20 caractères');
        }

        // Commentaire optionnel mais limité
        if (!$this->isEmpty('commentaire')) {
            $this->validateMaxLength('commentaire', 2000, 'Le commentaire ne doit pas dépasser 2000 caractères');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide le démarrage d'un nouveau tour
     *
     * @param array<string, mixed> $data
     */
    public function validateNouveauTour(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Session obligatoire
        $this->validateRequired('session_id', 'La session est obligatoire');
        $this->validatePositiveInteger('session_id');

        // Tour actuel pour vérification
        if (!$this->isEmpty('tour_actuel')) {
            $tour = (int) $this->data['tour_actuel'];
            if ($tour >= 3) {
                $this->addError('tour', 'Le nombre maximum de tours (3) a été atteint');
            }
        }

        // Délai pour le nouveau tour
        if (!$this->isEmpty('delai_heures')) {
            $this->validatePositiveInteger('delai_heures');
            $delai = (int) $this->data['delai_heures'];
            if ($delai < 24 || $delai > 168) {
                $this->addError('delai_heures', 'Le délai doit être entre 24 et 168 heures');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Valide l'escalade au doyen
     *
     * @param array<string, mixed> $data
     */
    public function validateEscalade(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Session obligatoire
        $this->validateRequired('session_id', 'La session est obligatoire');
        $this->validatePositiveInteger('session_id');

        // Motif d'escalade obligatoire
        $this->validateRequired('motif', 'Le motif d\'escalade est obligatoire');
        $this->validateMinLength('motif', 50, 'Le motif doit contenir au moins 50 caractères');
        $this->validateMaxLength('motif', 2000);

        // Rapports concernés
        $this->validateRequired('rapports', 'Les rapports concernés sont obligatoires');
        $this->validateArray('rapports');

        return !$this->hasErrors();
    }

    /**
     * Valide la décision arbitrale du doyen
     *
     * @param array<string, mixed> $data
     */
    public function validateDecisionArbitrale(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Session obligatoire
        $this->validateRequired('session_id', 'La session est obligatoire');
        $this->validatePositiveInteger('session_id');

        // Rapport obligatoire
        $this->validateRequired('rapport_id', 'Le rapport est obligatoire');
        $this->validatePositiveInteger('rapport_id');

        // Décision obligatoire
        $this->validateRequired('decision', 'La décision est obligatoire');
        $this->validateInArray('decision', ['valider', 'rejeter'], 'Décision invalide');

        // Justification obligatoire
        $this->validateRequired('justification', 'La justification est obligatoire');
        $this->validateMinLength('justification', 100, 'La justification doit contenir au moins 100 caractères');
        $this->validateMaxLength('justification', 5000);

        return !$this->hasErrors();
    }
}
