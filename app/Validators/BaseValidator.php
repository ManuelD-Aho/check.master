<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur de base
 * 
 * Classe abstraite fournissant les méthodes communes de validation.
 * Tous les validateurs doivent étendre cette classe.
 */
abstract class BaseValidator
{
    /**
     * Erreurs de validation
     *
     * @var array<string, string>
     */
    protected array $errors = [];

    /**
     * Données à valider
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Valide les données
     *
     * @param array<string, mixed> $data
     */
    abstract public function validate(array $data): bool;

    /**
     * Retourne les erreurs de validation
     *
     * @return array<string, string>
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

    /**
     * Vérifie s'il y a des erreurs
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Ajoute une erreur
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    /**
     * Réinitialise les erreurs
     */
    protected function resetErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Vérifie si un champ est vide
     */
    protected function isEmpty(string $field): bool
    {
        return !isset($this->data[$field]) || $this->data[$field] === '' || $this->data[$field] === null;
    }

    /**
     * Valide un champ obligatoire
     */
    protected function validateRequired(string $field, string $message = ''): bool
    {
        if ($this->isEmpty($field)) {
            $this->addError($field, $message ?: "Le champ {$field} est obligatoire");
            return false;
        }
        return true;
    }

    /**
     * Valide une longueur minimale
     */
    protected function validateMinLength(string $field, int $min, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && strlen((string) $this->data[$field]) < $min) {
            $this->addError($field, $message ?: "Le champ {$field} doit contenir au moins {$min} caractères");
            return false;
        }
        return true;
    }

    /**
     * Valide une longueur maximale
     */
    protected function validateMaxLength(string $field, int $max, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && strlen((string) $this->data[$field]) > $max) {
            $this->addError($field, $message ?: "Le champ {$field} ne doit pas dépasser {$max} caractères");
            return false;
        }
        return true;
    }

    /**
     * Valide un email
     */
    protected function validateEmail(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?: 'Format d\'email invalide');
            return false;
        }
        return true;
    }

    /**
     * Valide un numéro de téléphone (format Côte d'Ivoire)
     */
    protected function validatePhone(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $phone = (string) $this->data[$field];
            if (!preg_match('/^(\+225)?[0-9]{10}$/', $phone)) {
                $this->addError($field, $message ?: 'Format de téléphone invalide');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide une date au format Y-m-d
     */
    protected function validateDate(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $date = \DateTime::createFromFormat('Y-m-d', (string) $this->data[$field]);
            if (!$date || $date->format('Y-m-d') !== $this->data[$field]) {
                $this->addError($field, $message ?: 'Format de date invalide (AAAA-MM-JJ attendu)');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide une date dans le futur
     */
    protected function validateFutureDate(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $date = \DateTime::createFromFormat('Y-m-d', (string) $this->data[$field]);
            if ($date && $date <= new \DateTime('today')) {
                $this->addError($field, $message ?: 'La date doit être dans le futur');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide une date dans le passé
     */
    protected function validatePastDate(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $date = \DateTime::createFromFormat('Y-m-d', (string) $this->data[$field]);
            if ($date && $date >= new \DateTime('today')) {
                $this->addError($field, $message ?: 'La date doit être dans le passé');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide un entier positif
     */
    protected function validatePositiveInteger(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $value = $this->data[$field];
            if (!is_numeric($value) || (int) $value <= 0 || (int) $value != $value) {
                $this->addError($field, $message ?: 'La valeur doit être un entier positif');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide un décimal positif
     */
    protected function validatePositiveDecimal(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $value = $this->data[$field];
            if (!is_numeric($value) || (float) $value <= 0) {
                $this->addError($field, $message ?: 'La valeur doit être un nombre positif');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide une valeur dans une liste
     *
     * @param array<mixed> $allowedValues
     */
    protected function validateInArray(string $field, array $allowedValues, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && !in_array($this->data[$field], $allowedValues, true)) {
            $this->addError($field, $message ?: 'Valeur non autorisée');
            return false;
        }
        return true;
    }

    /**
     * Valide une expression régulière
     */
    protected function validateRegex(string $field, string $pattern, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && !preg_match($pattern, (string) $this->data[$field])) {
            $this->addError($field, $message ?: 'Format invalide');
            return false;
        }
        return true;
    }

    /**
     * Valide une URL
     */
    protected function validateUrl(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->addError($field, $message ?: 'Format d\'URL invalide');
            return false;
        }
        return true;
    }

    /**
     * Valide un JSON
     */
    protected function validateJson(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            json_decode((string) $this->data[$field]);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError($field, $message ?: 'Format JSON invalide');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide que deux champs sont identiques
     */
    protected function validateMatch(string $field, string $matchField, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && $this->data[$field] !== ($this->data[$matchField] ?? null)) {
            $this->addError($field, $message ?: 'Les champs ne correspondent pas');
            return false;
        }
        return true;
    }

    /**
     * Valide une plage de valeurs
     */
    protected function validateBetween(string $field, float $min, float $max, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $value = (float) $this->data[$field];
            if ($value < $min || $value > $max) {
                $this->addError($field, $message ?: "La valeur doit être entre {$min} et {$max}");
                return false;
            }
        }
        return true;
    }

    /**
     * Valide un booléen
     */
    protected function validateBoolean(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field)) {
            $value = $this->data[$field];
            if (!in_array($value, [true, false, 0, 1, '0', '1', 'true', 'false'], true)) {
                $this->addError($field, $message ?: 'La valeur doit être un booléen');
                return false;
            }
        }
        return true;
    }

    /**
     * Valide un tableau
     */
    protected function validateArray(string $field, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && !is_array($this->data[$field])) {
            $this->addError($field, $message ?: 'La valeur doit être un tableau');
            return false;
        }
        return true;
    }

    /**
     * Valide un tableau avec un nombre minimum d'éléments
     */
    protected function validateArrayMin(string $field, int $min, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && is_array($this->data[$field])) {
            if (count($this->data[$field]) < $min) {
                $this->addError($field, $message ?: "Le tableau doit contenir au moins {$min} éléments");
                return false;
            }
        }
        return true;
    }

    /**
     * Valide un fichier uploadé
     *
     * @param array<string> $allowedTypes Types MIME autorisés
     */
    protected function validateFile(string $field, array $allowedTypes = [], int $maxSize = 0, string $message = ''): bool
    {
        if (!$this->isEmpty($field) && isset($_FILES[$field])) {
            $file = $_FILES[$field];
            
            // Vérifier les erreurs d'upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->addError($field, $message ?: 'Erreur lors de l\'upload du fichier');
                return false;
            }

            // Vérifier le type MIME
            if (!empty($allowedTypes)) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);
                if (!in_array($mimeType, $allowedTypes, true)) {
                    $this->addError($field, $message ?: 'Type de fichier non autorisé');
                    return false;
                }
            }

            // Vérifier la taille
            if ($maxSize > 0 && $file['size'] > $maxSize) {
                $maxSizeMb = round($maxSize / 1024 / 1024, 2);
                $this->addError($field, $message ?: "Le fichier ne doit pas dépasser {$maxSizeMb} Mo");
                return false;
            }
        }
        return true;
    }
}
