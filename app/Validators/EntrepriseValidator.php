<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Entreprise
 * 
 * Valide les données des entreprises partenaires.
 */
class EntrepriseValidator extends BaseValidator
{
    /**
     * Secteurs d'activité valides
     */
    private const SECTEURS_VALIDES = [
        'informatique',
        'telecom',
        'banque',
        'assurance',
        'industrie',
        'commerce',
        'services',
        'transport',
        'energie',
        'sante',
        'education',
        'agriculture',
        'autre',
    ];

    /**
     * Statuts juridiques valides
     */
    private const STATUTS_JURIDIQUES = [
        'SA',
        'SARL',
        'SAS',
        'EURL',
        'SNC',
        'GIE',
        'ASSOCIATION',
        'ADMINISTRATION',
        'ONG',
        'AUTRE',
    ];

    /**
     * Valide les données de l'entreprise
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nom obligatoire
        $this->validateRequired('nom', 'Le nom de l\'entreprise est obligatoire');
        $this->validateMinLength('nom', 2, 'Le nom doit contenir au moins 2 caractères');
        $this->validateMaxLength('nom', 200, 'Le nom ne doit pas dépasser 200 caractères');

        // Secteur d'activité
        if (!$this->isEmpty('secteur')) {
            $this->validateInArray('secteur', self::SECTEURS_VALIDES, 'Secteur d\'activité invalide');
        }

        // Statut juridique
        if (!$this->isEmpty('statut_juridique')) {
            $this->validateInArray('statut_juridique', self::STATUTS_JURIDIQUES, 'Statut juridique invalide');
        }

        // Email
        if (!$this->isEmpty('email')) {
            $this->validateEmail('email', 'Format d\'email invalide');
        }

        // Téléphone
        if (!$this->isEmpty('telephone')) {
            $this->validatePhone('telephone', 'Format de téléphone invalide');
        }

        // Site web
        if (!$this->isEmpty('site_web')) {
            $this->validateUrl('site_web', 'Format d\'URL invalide');
        }

        // Adresse
        if (!$this->isEmpty('adresse')) {
            $this->validateMaxLength('adresse', 500, 'L\'adresse ne doit pas dépasser 500 caractères');
        }

        // Ville
        if (!$this->isEmpty('ville')) {
            $this->validateMaxLength('ville', 100, 'La ville ne doit pas dépasser 100 caractères');
        }

        // Code postal
        if (!$this->isEmpty('code_postal')) {
            $this->validateMaxLength('code_postal', 20, 'Le code postal ne doit pas dépasser 20 caractères');
        }

        // Pays
        if (!$this->isEmpty('pays')) {
            $this->validateMaxLength('pays', 100, 'Le pays ne doit pas dépasser 100 caractères');
        }

        // Contact principal
        $this->validateContact();

        return !$this->hasErrors();
    }

    /**
     * Valide les informations du contact principal
     */
    private function validateContact(): void
    {
        // Nom du contact
        if (!$this->isEmpty('contact_nom')) {
            $this->validateMaxLength('contact_nom', 100, 'Le nom du contact ne doit pas dépasser 100 caractères');
        }

        // Email du contact
        if (!$this->isEmpty('contact_email')) {
            $this->validateEmail('contact_email', 'Format d\'email du contact invalide');
        }

        // Téléphone du contact
        if (!$this->isEmpty('contact_telephone')) {
            $this->validatePhone('contact_telephone', 'Format de téléphone du contact invalide');
        }

        // Fonction du contact
        if (!$this->isEmpty('contact_fonction')) {
            $this->validateMaxLength('contact_fonction', 100, 'La fonction ne doit pas dépasser 100 caractères');
        }
    }

    /**
     * Valide les données pour la création d'entreprise
     *
     * @param array<string, mixed> $data
     */
    public function validateCreation(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nom obligatoire
        $this->validateRequired('nom', 'Le nom de l\'entreprise est obligatoire');
        $this->validateMinLength('nom', 2, 'Le nom doit contenir au moins 2 caractères');
        $this->validateMaxLength('nom', 200);

        // Secteur obligatoire pour création
        $this->validateRequired('secteur', 'Le secteur d\'activité est obligatoire');
        $this->validateInArray('secteur', self::SECTEURS_VALIDES);

        // Au moins un moyen de contact obligatoire
        if ($this->isEmpty('email') && $this->isEmpty('telephone')) {
            $this->addError('contact', 'Au moins un email ou un téléphone est obligatoire');
        }

        // Valider email si présent
        if (!$this->isEmpty('email')) {
            $this->validateEmail('email');
        }

        // Valider téléphone si présent
        if (!$this->isEmpty('telephone')) {
            $this->validatePhone('telephone');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide les données d'un tuteur/maître de stage
     *
     * @param array<string, mixed> $data
     */
    public function validateTuteur(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nom obligatoire
        $this->validateRequired('nom', 'Le nom du tuteur est obligatoire');
        $this->validateMaxLength('nom', 100);

        // Prénom
        if (!$this->isEmpty('prenom')) {
            $this->validateMaxLength('prenom', 100);
        }

        // Email obligatoire
        $this->validateRequired('email', 'L\'email du tuteur est obligatoire');
        $this->validateEmail('email');

        // Téléphone
        if (!$this->isEmpty('telephone')) {
            $this->validatePhone('telephone');
        }

        // Fonction obligatoire
        $this->validateRequired('fonction', 'La fonction du tuteur est obligatoire');
        $this->validateMaxLength('fonction', 100);

        // Entreprise obligatoire
        $this->validateRequired('entreprise_id', 'L\'entreprise est obligatoire');
        $this->validatePositiveInteger('entreprise_id');

        return !$this->hasErrors();
    }
}
