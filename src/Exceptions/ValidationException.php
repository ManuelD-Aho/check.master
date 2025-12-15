<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception de validation
 * 
 * Lancée quand les données soumises ne passent pas la validation.
 * Code HTTP: 422 Unprocessable Entity
 */
class ValidationException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'VALIDATION_ERROR';
    private array $errors = [];

    /**
     * @param array $errors Tableau des erreurs de validation ['champ' => 'message']
     * @param string $message Message principal
     */
    public function __construct(
        array $errors = [],
        string $message = 'Les données soumises sont invalides'
    ) {
        parent::__construct($message, 422, 'VALIDATION_ERROR', ['errors' => $errors]);
        $this->errors = $errors;
    }

    /**
     * Retourne les erreurs de validation
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Vérifie si un champ a une erreur
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Retourne l'erreur d'un champ spécifique
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Ajoute une erreur
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        $this->details['errors'] = $this->errors;
        return $this;
    }

    /**
     * Retourne la première erreur
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'code' => $this->errorCode,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ];
    }

    /**
     * Factory depuis un validateur
     */
    public static function fromErrors(array $errors, string $message = 'Les données soumises sont invalides'): self
    {
        return new self($errors, $message);
    }
}
