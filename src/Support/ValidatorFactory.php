<?php

declare(strict_types=1);

namespace Src\Support;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Factory pour la validation Symfony
 * 
 * Fournit une interface simplifiée pour la validation
 * avec contraintes prédéfinies pour CheckMaster.
 */
class ValidatorFactory
{
    private static ?ValidatorInterface $validator = null;

    /**
     * Retourne l'instance du validateur Symfony
     */
    public static function getInstance(): ValidatorInterface
    {
        if (self::$validator === null) {
            self::$validator = Validation::createValidator();
        }

        return self::$validator;
    }

    /**
     * Valide une valeur contre des contraintes
     *
     * @param mixed $value Valeur à valider
     * @param array $constraints Contraintes Symfony
     * @return array Tableau d'erreurs (vide si valide)
     */
    public static function validate(mixed $value, array $constraints): array
    {
        $violations = self::getInstance()->validate($value, $constraints);
        return self::formatViolations($violations);
    }

    /**
     * Valide un tableau de données
     *
     * @param array $data Données à valider
     * @param array $rules Règles ['field' => [contraintes]]
     * @return array Erreurs par champ
     */
    public static function validateArray(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $constraints) {
            $value = $data[$field] ?? null;
            $fieldErrors = self::validate($value, $constraints);

            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors[0]; // Première erreur seulement
            }
        }

        return $errors;
    }

    /**
     * Formate les violations en tableau de messages
     */
    private static function formatViolations(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }

    // ========== Contraintes Prédéfinies ==========

    /**
     * Contrainte: Email valide
     */
    public static function email(string $message = 'Adresse email invalide'): Assert\Email
    {
        return new Assert\Email(message: $message);
    }

    /**
     * Contrainte: Champ requis (non vide)
     */
    public static function required(string $message = 'Ce champ est requis'): Assert\NotBlank
    {
        return new Assert\NotBlank(message: $message);
    }

    /**
     * Contrainte: Longueur minimum/maximum
     */
    public static function length(
        ?int $min = null,
        ?int $max = null,
        ?string $minMessage = null,
        ?string $maxMessage = null
    ): Assert\Length {
        return new Assert\Length(
            min: $min,
            max: $max,
            minMessage: $minMessage ?? "Ce champ doit contenir au moins {{ limit }} caractères",
            maxMessage: $maxMessage ?? "Ce champ ne doit pas dépasser {{ limit }} caractères"
        );
    }

    /**
     * Contrainte: Mot de passe sécurisé
     */
    public static function password(int $minLength = 8): array
    {
        return [
            new Assert\NotBlank(message: 'Le mot de passe est requis'),
            new Assert\Length(
                min: $minLength,
                minMessage: "Le mot de passe doit contenir au moins {$minLength} caractères"
            ),
            new Assert\Regex(
                pattern: '/[A-Z]/',
                message: 'Le mot de passe doit contenir au moins une majuscule'
            ),
            new Assert\Regex(
                pattern: '/[a-z]/',
                message: 'Le mot de passe doit contenir au moins une minuscule'
            ),
            new Assert\Regex(
                pattern: '/[0-9]/',
                message: 'Le mot de passe doit contenir au moins un chiffre'
            ),
        ];
    }

    /**
     * Contrainte: Numéro de téléphone
     */
    public static function phone(string $message = 'Numéro de téléphone invalide'): Assert\Regex
    {
        return new Assert\Regex(
            pattern: '/^(\+225)?[0-9]{10}$/',
            message: $message
        );
    }

    /**
     * Contrainte: Numéro étudiant CheckMaster
     */
    public static function studentNumber(string $message = 'Numéro étudiant invalide'): Assert\Regex
    {
        return new Assert\Regex(
            pattern: '/^[A-Z]{2}[0-9]{8}$/',
            message: $message
        );
    }

    /**
     * Contrainte: Date au format Y-m-d
     */
    public static function date(string $message = 'Date invalide'): Assert\Date
    {
        return new Assert\Date(message: $message);
    }

    /**
     * Contrainte: Entier positif
     */
    public static function positiveInteger(string $message = 'La valeur doit être un entier positif'): Assert\Positive
    {
        return new Assert\Positive(message: $message);
    }

    /**
     * Contrainte: Valeur dans une liste
     */
    public static function choice(array $choices, string $message = 'Valeur invalide'): Assert\Choice
    {
        return new Assert\Choice(choices: $choices, message: $message);
    }

    /**
     * Contrainte: URL valide
     */
    public static function url(string $message = 'URL invalide'): Assert\Url
    {
        return new Assert\Url(message: $message);
    }

    /**
     * Contrainte: Regex personnalisée
     */
    public static function regex(string $pattern, string $message = 'Format invalide'): Assert\Regex
    {
        return new Assert\Regex(pattern: $pattern, message: $message);
    }

    /**
     * Contrainte: Montant (décimal positif)
     */
    public static function amount(string $message = 'Montant invalide'): array
    {
        return [
            new Assert\NotBlank(message: 'Le montant est requis'),
            new Assert\Type(type: 'numeric', message: $message),
            new Assert\PositiveOrZero(message: 'Le montant doit être positif ou nul'),
        ];
    }

    /**
     * Réinitialise l'instance (pour les tests)
     */
    public static function reset(): void
    {
        self::$validator = null;
    }
}
