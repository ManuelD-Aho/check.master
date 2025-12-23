<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Configuration
 * 
 * Valide les paramètres de configuration système.
 */
class ConfigurationValidator extends BaseValidator
{
    /**
     * Catégories de configuration valides
     */
    private const CATEGORIES_VALIDES = [
        'app',
        'email',
        'security',
        'workflow',
        'notification',
        'pdf',
        'sla',
        'commission',
        'signature',
    ];

    /**
     * Types de valeur valides
     */
    private const TYPES_VALEURS = [
        'string',
        'integer',
        'float',
        'boolean',
        'json',
        'array',
    ];

    /**
     * Valide les données de configuration
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Clé obligatoire
        $this->validateRequired('cle', 'La clé de configuration est obligatoire');
        $this->validateMaxLength('cle', 100, 'La clé ne doit pas dépasser 100 caractères');
        $this->validateRegex('cle', '/^[a-z][a-z0-9_.]*$/', 'La clé doit être en minuscules avec points comme séparateurs');

        // Valeur obligatoire
        $this->validateRequired('valeur', 'La valeur est obligatoire');

        // Catégorie si présente
        if (!$this->isEmpty('categorie')) {
            $this->validateInArray('categorie', self::CATEGORIES_VALIDES, 'Catégorie invalide');
        }

        // Type de valeur si présent
        if (!$this->isEmpty('type_valeur')) {
            $this->validateInArray('type_valeur', self::TYPES_VALEURS, 'Type de valeur invalide');
            $this->validateTypeValue();
        }

        // Description
        if (!$this->isEmpty('description')) {
            $this->validateMaxLength('description', 500, 'La description ne doit pas dépasser 500 caractères');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide la cohérence entre le type déclaré et la valeur
     */
    private function validateTypeValue(): void
    {
        if ($this->isEmpty('type_valeur') || $this->isEmpty('valeur')) {
            return;
        }

        $type = (string) $this->data['type_valeur'];
        $value = $this->data['valeur'];

        switch ($type) {
            case 'integer':
                if (!is_numeric($value) || (int) $value != $value) {
                    $this->addError('valeur', 'La valeur doit être un entier');
                }
                break;

            case 'float':
                if (!is_numeric($value)) {
                    $this->addError('valeur', 'La valeur doit être un nombre');
                }
                break;

            case 'boolean':
                if (!in_array($value, ['true', 'false', '1', '0', true, false, 1, 0], true)) {
                    $this->addError('valeur', 'La valeur doit être un booléen (true/false)');
                }
                break;

            case 'json':
            case 'array':
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->addError('valeur', 'La valeur doit être un JSON valide');
                    }
                }
                break;
        }
    }

    /**
     * Valide les paramètres email
     *
     * @param array<string, mixed> $data
     */
    public function validateEmailSettings(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Host SMTP
        if (!$this->isEmpty('smtp_host')) {
            $this->validateMaxLength('smtp_host', 255);
        }

        // Port SMTP
        if (!$this->isEmpty('smtp_port')) {
            $this->validatePositiveInteger('smtp_port', 'Port SMTP invalide');
            $port = (int) $this->data['smtp_port'];
            if ($port < 1 || $port > 65535) {
                $this->addError('smtp_port', 'Le port doit être entre 1 et 65535');
            }
        }

        // Email expéditeur
        if (!$this->isEmpty('from_email')) {
            $this->validateEmail('from_email', 'Email expéditeur invalide');
        }

        // Encryption
        if (!$this->isEmpty('encryption')) {
            $this->validateInArray('encryption', ['tls', 'ssl', 'none'], 'Type de chiffrement invalide');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide les paramètres de sécurité
     *
     * @param array<string, mixed> $data
     */
    public function validateSecuritySettings(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Durée session
        if (!$this->isEmpty('session_lifetime')) {
            $this->validatePositiveInteger('session_lifetime', 'Durée de session invalide');
            $lifetime = (int) $this->data['session_lifetime'];
            if ($lifetime < 300 || $lifetime > 86400) {
                $this->addError('session_lifetime', 'La durée de session doit être entre 5 minutes et 24 heures');
            }
        }

        // Longueur minimale mot de passe
        if (!$this->isEmpty('password_min_length')) {
            $this->validatePositiveInteger('password_min_length');
            $minLength = (int) $this->data['password_min_length'];
            if ($minLength < 8 || $minLength > 128) {
                $this->addError('password_min_length', 'La longueur minimale doit être entre 8 et 128');
            }
        }

        // Rate limit
        if (!$this->isEmpty('rate_limit_requests')) {
            $this->validatePositiveInteger('rate_limit_requests');
        }

        if (!$this->isEmpty('rate_limit_window')) {
            $this->validatePositiveInteger('rate_limit_window');
        }

        return !$this->hasErrors();
    }

    /**
     * Valide les paramètres SLA
     *
     * @param array<string, mixed> $data
     */
    public function validateSlaSettings(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Délais en jours
        $delaiFields = [
            'delai_scolarite',
            'delai_communication',
            'delai_commission',
            'delai_encadreur',
            'delai_jury',
            'delai_corrections',
        ];

        foreach ($delaiFields as $field) {
            if (!$this->isEmpty($field)) {
                $this->validatePositiveInteger($field, "Délai {$field} invalide");
                $value = (int) $this->data[$field];
                if ($value > 365) {
                    $this->addError($field, 'Le délai ne peut pas dépasser 365 jours');
                }
            }
        }

        // Seuils d'alerte (pourcentage)
        $seuilFields = ['seuil_rappel', 'seuil_alerte', 'seuil_escalade'];
        foreach ($seuilFields as $field) {
            if (!$this->isEmpty($field)) {
                $this->validatePositiveInteger($field);
                $value = (int) $this->data[$field];
                if ($value > 100) {
                    $this->addError($field, 'Le seuil doit être un pourcentage (0-100)');
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Valide les paramètres de commission
     *
     * @param array<string, mixed> $data
     */
    public function validateCommissionSettings(array $data): bool
    {
        $this->resetErrors();
        $this->data = $data;

        // Nombre de tours max
        if (!$this->isEmpty('tours_max')) {
            $this->validatePositiveInteger('tours_max');
            $tours = (int) $this->data['tours_max'];
            if ($tours > 5) {
                $this->addError('tours_max', 'Le nombre de tours ne peut pas dépasser 5');
            }
        }

        // Délai par tour (en heures)
        if (!$this->isEmpty('delai_tour_heures')) {
            $this->validatePositiveInteger('delai_tour_heures');
            $heures = (int) $this->data['delai_tour_heures'];
            if ($heures > 168) { // 7 jours
                $this->addError('delai_tour_heures', 'Le délai par tour ne peut pas dépasser 168 heures (7 jours)');
            }
        }

        // Quorum
        if (!$this->isEmpty('quorum_min')) {
            $this->validatePositiveInteger('quorum_min');
        }

        return !$this->hasErrors();
    }
}
