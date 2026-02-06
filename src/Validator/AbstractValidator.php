<?php

declare(strict_types=1);

namespace App\Validator;

abstract class AbstractValidator
{
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function isValid(): bool
    {
        return !$this->hasErrors();
    }

    protected function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    protected function reset(): void
    {
        $this->errors = [];
    }

    protected function getLabel(string $field, string $label): string
    {
        return $label !== '' ? $label : $field;
    }

    protected function validateRequired(string $field, mixed $value, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
            $this->addError($field, "Le champ {$l} est obligatoire.");
            return false;
        }

        return true;
    }

    protected function validateString(string $field, mixed $value, int $minLength = 1, int $maxLength = 255, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_string($value)) {
            $this->addError($field, "Le champ {$l} doit être une chaîne de caractères.");
            return false;
        }

        $length = mb_strlen($value);

        if ($length < $minLength) {
            $this->addError($field, "Le champ {$l} doit contenir au moins {$minLength} caractère(s).");
            return false;
        }

        if ($length > $maxLength) {
            $this->addError($field, "Le champ {$l} ne doit pas dépasser {$maxLength} caractère(s).");
            return false;
        }

        return true;
    }

    protected function validateEmail(string $field, mixed $value, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_string($value) || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, "Le champ {$l} doit être une adresse email valide.");
            return false;
        }

        return true;
    }

    protected function validateDate(string $field, mixed $value, string $format = 'Y-m-d', string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_string($value)) {
            $this->addError($field, "Le champ {$l} doit être une date valide au format {$format}.");
            return false;
        }

        $date = \DateTimeImmutable::createFromFormat($format, $value);

        if ($date === false || $date->format($format) !== $value) {
            $this->addError($field, "Le champ {$l} doit être une date valide au format {$format}.");
            return false;
        }

        return true;
    }

    protected function validateNumeric(string $field, mixed $value, ?float $min = null, ?float $max = null, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_numeric($value)) {
            $this->addError($field, "Le champ {$l} doit être une valeur numérique.");
            return false;
        }

        $floatVal = (float) $value;

        if ($min !== null && $floatVal < $min) {
            $this->addError($field, "Le champ {$l} doit être supérieur ou égal à {$min}.");
            return false;
        }

        if ($max !== null && $floatVal > $max) {
            $this->addError($field, "Le champ {$l} doit être inférieur ou égal à {$max}.");
            return false;
        }

        return true;
    }

    protected function validateIn(string $field, mixed $value, array $allowed, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!in_array($value, $allowed, true)) {
            $allowedStr = implode(', ', $allowed);
            $this->addError($field, "Le champ {$l} doit être l'une des valeurs suivantes : {$allowedStr}.");
            return false;
        }

        return true;
    }

    protected function validateRegex(string $field, mixed $value, string $pattern, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_string($value) || preg_match($pattern, $value) !== 1) {
            $this->addError($field, "Le champ {$l} n'a pas un format valide.");
            return false;
        }

        return true;
    }

    protected function validateInteger(string $field, mixed $value, ?int $min = null, ?int $max = null, string $label = ''): bool
    {
        $l = $this->getLabel($field, $label);

        if (!is_int($value) && !(is_string($value) && ctype_digit($value))) {
            $this->addError($field, "Le champ {$l} doit être un nombre entier.");
            return false;
        }

        $intVal = (int) $value;

        if ($min !== null && $intVal < $min) {
            $this->addError($field, "Le champ {$l} doit être supérieur ou égal à {$min}.");
            return false;
        }

        if ($max !== null && $intVal > $max) {
            $this->addError($field, "Le champ {$l} doit être inférieur ou égal à {$max}.");
            return false;
        }

        return true;
    }
}
