<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Jury Membre
 * 
 * Valide les données relatives aux membres du jury de soutenance.
 */
class JuryMembreValidator extends BaseValidator
{
    /**
     * Rôles valides dans le jury
     */
    private const ROLES_VALIDES = [
        'president',
        'rapporteur',
        'examinateur',
        'membre',
        'invite',
    ];

    /**
     * Statuts d'invitation valides
     */
    private const STATUTS_INVITATION = [
        'en_attente',
        'accepte',
        'refuse',
        'indisponible',
    ];

    /**
     * Nombre minimum de membres dans un jury
     */
    private const MIN_MEMBRES = 3;

    /**
     * Nombre maximum de membres dans un jury
     */
    private const MAX_MEMBRES = 5;

    /**
     * Valide les données d'un membre du jury
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Soutenance obligatoire
        $this->validateRequired('soutenance_id', 'La soutenance est obligatoire');
        $this->validatePositiveInteger('soutenance_id');

        // Enseignant obligatoire
        $this->validateRequired('enseignant_id', 'L\'enseignant est obligatoire');
        $this->validatePositiveInteger('enseignant_id');

        // Rôle obligatoire
        $this->validateRequired('role', 'Le rôle est obligatoire');
        $this->validateInArray('role', self::ROLES_VALIDES, 'Rôle invalide');

        // Statut si présent
        if (!$this->isEmpty('statut')) {
            $this->validateInArray('statut', self::STATUTS_INVITATION, 'Statut invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide la composition complète d'un jury
     *
     * @param array<array<string, mixed>> $membres Liste des membres
     */
    public function validateComposition(array $membres): bool
    {
        $this->resetErrors();

        // Vérifier le nombre de membres
        $count = count($membres);
        if ($count < self::MIN_MEMBRES) {
            $this->addError('membres', 'Le jury doit avoir au moins ' . self::MIN_MEMBRES . ' membres');
            return false;
        }
        if ($count > self::MAX_MEMBRES) {
            $this->addError('membres', 'Le jury ne peut pas avoir plus de ' . self::MAX_MEMBRES . ' membres');
            return false;
        }

        // Vérifier qu'il y a un président
        $hasPresident = false;
        $hasRapporteur = false;
        $enseignantIds = [];
        $roles = [];

        foreach ($membres as $index => $membre) {
            // Vérifier que chaque membre a les champs requis
            if (empty($membre['enseignant_id'])) {
                $this->addError("membre_{$index}", 'Enseignant manquant');
                continue;
            }

            if (empty($membre['role'])) {
                $this->addError("membre_{$index}", 'Rôle manquant');
                continue;
            }

            // Vérifier les doublons
            if (in_array($membre['enseignant_id'], $enseignantIds, true)) {
                $this->addError("membre_{$index}", 'Un enseignant ne peut pas être présent plusieurs fois dans le jury');
            }
            $enseignantIds[] = $membre['enseignant_id'];

            // Compter les rôles
            $role = $membre['role'];
            $roles[$role] = ($roles[$role] ?? 0) + 1;

            if ($role === 'president') {
                $hasPresident = true;
            }
            if ($role === 'rapporteur') {
                $hasRapporteur = true;
            }
        }

        // Vérifier la présence d'un président unique
        if (!$hasPresident) {
            $this->addError('president', 'Le jury doit avoir un président');
        }
        if (($roles['president'] ?? 0) > 1) {
            $this->addError('president', 'Le jury ne peut avoir qu\'un seul président');
        }

        // Vérifier la présence d'au moins un rapporteur
        if (!$hasRapporteur) {
            $this->addError('rapporteur', 'Le jury doit avoir au moins un rapporteur');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide une réponse à une invitation
     *
     * @param array<string, mixed> $data
     */
    public function validateReponse(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Réponse obligatoire
        $this->validateRequired('reponse', 'La réponse est obligatoire');
        $this->validateInArray('reponse', ['accepte', 'refuse', 'indisponible'], 'Réponse invalide');

        // Motif obligatoire si refus ou indisponibilité
        if (!$this->isEmpty('reponse') && in_array($this->data['reponse'], ['refuse', 'indisponible'], true)) {
            $this->validateRequired('motif', 'Le motif est obligatoire en cas de refus');
            $this->validateMinLength('motif', 10, 'Le motif doit contenir au moins 10 caractères');
            $this->validateMaxLength('motif', 500);
        }

        // Dates de disponibilité alternatives si indisponible
        if (!$this->isEmpty('reponse') && $this->data['reponse'] === 'indisponible') {
            if (!$this->isEmpty('dates_alternatives')) {
                $this->validateArray('dates_alternatives');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Valide le remplacement d'un membre
     *
     * @param array<string, mixed> $data
     */
    public function validateRemplacement(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Membre à remplacer obligatoire
        $this->validateRequired('membre_id', 'Le membre à remplacer est obligatoire');
        $this->validatePositiveInteger('membre_id');

        // Nouveau membre obligatoire
        $this->validateRequired('nouveau_enseignant_id', 'Le nouvel enseignant est obligatoire');
        $this->validatePositiveInteger('nouveau_enseignant_id');

        // Motif obligatoire
        $this->validateRequired('motif', 'Le motif de remplacement est obligatoire');
        $this->validateMinLength('motif', 10);
        $this->validateMaxLength('motif', 500);

        return !$this->hasErrors();
    }

    /**
     * Valide la désignation du président
     *
     * @param array<string, mixed> $data
     */
    public function validateDesignationPresident(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Soutenance obligatoire
        $this->validateRequired('soutenance_id', 'La soutenance est obligatoire');
        $this->validatePositiveInteger('soutenance_id');

        // Membre obligatoire
        $this->validateRequired('membre_id', 'Le membre est obligatoire');
        $this->validatePositiveInteger('membre_id');

        return !$this->hasErrors();
    }

    /**
     * Valide l'envoi d'une convocation
     *
     * @param array<string, mixed> $data
     */
    public function validateConvocation(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Soutenance obligatoire
        $this->validateRequired('soutenance_id', 'La soutenance est obligatoire');
        $this->validatePositiveInteger('soutenance_id');

        // Date et heure obligatoires
        $this->validateRequired('date_soutenance', 'La date de soutenance est obligatoire');
        $this->validateDate('date_soutenance');

        $this->validateRequired('heure_soutenance', 'L\'heure de soutenance est obligatoire');
        $this->validateRegex('heure_soutenance', '/^([01]\d|2[0-3]):([0-5]\d)$/', 'Format d\'heure invalide');

        // Lieu obligatoire
        $this->validateRequired('lieu', 'Le lieu est obligatoire');
        $this->validateMaxLength('lieu', 200);

        return !$this->hasErrors();
    }
}
