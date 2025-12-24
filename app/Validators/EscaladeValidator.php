<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Escalade
 * 
 * Valide les données des escalades (SLA, commission, workflow).
 */
class EscaladeValidator extends BaseValidator
{
    /**
     * Types d'escalade valides
     */
    private const TYPES_ESCALADE = [
        'sla_expire',
        'commission_divergence',
        'workflow_bloque',
        'urgence',
        'exception',
    ];

    /**
     * Niveaux d'escalade valides
     */
    private const NIVEAUX_ESCALADE = [
        1 => 'responsable_niveau',
        2 => 'responsable_filiere',
        3 => 'doyen',
        4 => 'direction_generale',
    ];

    /**
     * Statuts d'escalade valides
     */
    private const STATUTS_ESCALADE = [
        'ouverte',
        'en_cours',
        'resolue',
        'fermee',
        'annulee',
    ];

    /**
     * Priorités valides
     */
    private const PRIORITES = [
        'normale',
        'elevee',
        'urgente',
        'critique',
    ];

    /**
     * Valide les données d'escalade
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Type d'escalade obligatoire
        $this->validateRequired('type', 'Le type d\'escalade est obligatoire');
        $this->validateInArray('type', self::TYPES_ESCALADE, 'Type d\'escalade invalide');

        // Niveau d'escalade obligatoire
        $this->validateRequired('niveau', 'Le niveau d\'escalade est obligatoire');
        if (!$this->isEmpty('niveau')) {
            $niveau = (int) $this->data['niveau'];
            if (!isset(self::NIVEAUX_ESCALADE[$niveau])) {
                $this->addError('niveau', 'Niveau d\'escalade invalide (1-4)');
            }
        }

        // Entité concernée
        $this->validateRequired('entite_type', 'Le type d\'entité est obligatoire');
        $this->validateRequired('entite_id', 'L\'ID de l\'entité est obligatoire');
        $this->validatePositiveInteger('entite_id');

        // Motif obligatoire
        $this->validateRequired('motif', 'Le motif est obligatoire');
        $this->validateMinLength('motif', 20, 'Le motif doit contenir au moins 20 caractères');
        $this->validateMaxLength('motif', 2000);

        // Priorité
        if (!$this->isEmpty('priorite')) {
            $this->validateInArray('priorite', self::PRIORITES, 'Priorité invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une escalade SLA
     *
     * @param array<string, mixed> $data
     */
    public function validateEscaladeSla(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Dossier obligatoire
        $this->validateRequired('dossier_id', 'Le dossier est obligatoire');
        $this->validatePositiveInteger('dossier_id');

        // Étape workflow concernée
        $this->validateRequired('etape_workflow', 'L\'étape du workflow est obligatoire');

        // Délai dépassé (en jours)
        $this->validateRequired('delai_depasse', 'Le délai dépassé est obligatoire');
        $this->validatePositiveInteger('delai_depasse');

        // Responsable actuel
        if (!$this->isEmpty('responsable_actuel_id')) {
            $this->validatePositiveInteger('responsable_actuel_id');
        }

        // Justification du dépassement (optionnelle mais validée si présente)
        if (!$this->isEmpty('justification_depassement')) {
            $this->validateMaxLength('justification_depassement', 2000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une escalade de commission (divergence après 3 tours)
     *
     * @param array<string, mixed> $data
     */
    public function validateEscaladeCommission(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Session de commission obligatoire
        $this->validateRequired('session_id', 'La session de commission est obligatoire');
        $this->validatePositiveInteger('session_id');

        // Rapport concerné obligatoire
        $this->validateRequired('rapport_id', 'Le rapport est obligatoire');
        $this->validatePositiveInteger('rapport_id');

        // Historique des votes obligatoire
        $this->validateRequired('historique_votes', 'L\'historique des votes est obligatoire');
        $this->validateArray('historique_votes');
        $this->validateArrayMin('historique_votes', 3, 'L\'historique doit contenir au moins 3 tours de votes');

        // Analyse de la divergence
        $this->validateRequired('analyse_divergence', 'L\'analyse de la divergence est obligatoire');
        $this->validateMinLength('analyse_divergence', 100, 'L\'analyse doit contenir au moins 100 caractères');
        $this->validateMaxLength('analyse_divergence', 5000);

        // Recommandation optionnelle
        if (!$this->isEmpty('recommandation')) {
            $this->validateMaxLength('recommandation', 2000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une prise en charge d'escalade
     *
     * @param array<string, mixed> $data
     */
    public function validatePriseEnCharge(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Escalade obligatoire
        $this->validateRequired('escalade_id', 'L\'escalade est obligatoire');
        $this->validatePositiveInteger('escalade_id');

        // Responsable de prise en charge
        $this->validateRequired('responsable_id', 'Le responsable est obligatoire');
        $this->validatePositiveInteger('responsable_id');

        // Commentaire optionnel
        if (!$this->isEmpty('commentaire')) {
            $this->validateMaxLength('commentaire', 1000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une résolution d'escalade
     *
     * @param array<string, mixed> $data
     */
    public function validateResolution(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Escalade obligatoire
        $this->validateRequired('escalade_id', 'L\'escalade est obligatoire');
        $this->validatePositiveInteger('escalade_id');

        // Décision obligatoire
        $this->validateRequired('decision', 'La décision est obligatoire');
        $this->validateMinLength('decision', 20);
        $this->validateMaxLength('decision', 2000);

        // Actions prises
        $this->validateRequired('actions_prises', 'Les actions prises sont obligatoires');
        $this->validateMinLength('actions_prises', 20);
        $this->validateMaxLength('actions_prises', 3000);

        // Nouveau délai accordé (optionnel)
        if (!$this->isEmpty('nouveau_delai_jours')) {
            $this->validatePositiveInteger('nouveau_delai_jours');
            $delai = (int) $this->data['nouveau_delai_jours'];
            if ($delai > 90) {
                $this->addError('nouveau_delai_jours', 'Le nouveau délai ne peut pas dépasser 90 jours');
            }
        }

        // Recommandations pour éviter récurrence
        if (!$this->isEmpty('recommandations')) {
            $this->validateMaxLength('recommandations', 2000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide la fermeture d'une escalade
     *
     * @param array<string, mixed> $data
     */
    public function validateFermeture(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Escalade obligatoire
        $this->validateRequired('escalade_id', 'L\'escalade est obligatoire');
        $this->validatePositiveInteger('escalade_id');

        // Motif de fermeture
        $this->validateRequired('motif_fermeture', 'Le motif de fermeture est obligatoire');
        $this->validateInArray('motif_fermeture', ['resolue', 'annulee', 'caduque', 'doublon'], 'Motif de fermeture invalide');

        // Commentaire final
        if (!$this->isEmpty('commentaire_final')) {
            $this->validateMaxLength('commentaire_final', 1000);
        }

        return !$this->hasErrors();
    }

    /**
     * Valide un transfert d'escalade (changement de niveau)
     *
     * @param array<string, mixed> $data
     */
    public function validateTransfert(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Escalade obligatoire
        $this->validateRequired('escalade_id', 'L\'escalade est obligatoire');
        $this->validatePositiveInteger('escalade_id');

        // Nouveau niveau obligatoire
        $this->validateRequired('nouveau_niveau', 'Le nouveau niveau est obligatoire');
        if (!$this->isEmpty('nouveau_niveau')) {
            $niveau = (int) $this->data['nouveau_niveau'];
            if (!isset(self::NIVEAUX_ESCALADE[$niveau])) {
                $this->addError('nouveau_niveau', 'Niveau d\'escalade invalide');
            }
        }

        // Motif de transfert
        $this->validateRequired('motif_transfert', 'Le motif de transfert est obligatoire');
        $this->validateMinLength('motif_transfert', 20);
        $this->validateMaxLength('motif_transfert', 1000);

        return !$this->hasErrors();
    }
}
