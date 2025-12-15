<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Conflit
 * 
 * Lancée quand une opération crée un conflit (doublon, état invalide, etc.).
 * Code HTTP: 409 Conflict
 */
class ConflictException extends AppException
{
    protected int $httpCode = 409;
    protected string $errorCode = 'CONFLICT';

    /**
     * @param string $message Message d'erreur
     * @param string $field Champ en conflit
     */
    public function __construct(
        string $message = 'Conflit détecté',
        string $field = ''
    ) {
        $details = [];
        if ($field !== '') {
            $details['field'] = $field;
        }

        parent::__construct($message, 409, 'CONFLICT', $details);
    }

    /**
     * Doublon détecté
     */
    public static function duplicate(string $field, mixed $value = null): self
    {
        $message = $value !== null
            ? "La valeur '{$value}' existe déjà pour le champ '{$field}'"
            : "Une entrée avec ce(tte) {$field} existe déjà";

        $exception = new self($message, $field);
        if ($value !== null) {
            $exception->details['value'] = $value;
        }
        return $exception;
    }

    /**
     * Email déjà utilisé
     */
    public static function emailExists(string $email): self
    {
        return self::duplicate('email', $email);
    }

    /**
     * Numéro étudiant déjà utilisé
     */
    public static function studentNumberExists(string $numero): self
    {
        return self::duplicate('num_etu', $numero);
    }

    /**
     * Transition workflow impossible
     */
    public static function invalidTransition(string $from, string $to): self
    {
        $exception = new self(
            "Transition impossible de '{$from}' vers '{$to}'",
            'workflow_state'
        );
        $exception->details['from_state'] = $from;
        $exception->details['to_state'] = $to;
        return $exception;
    }

    /**
     * Ressource verrouillée
     */
    public static function locked(string $resource): self
    {
        return new self(
            "Ce(tte) {$resource} est verrouillé(e) et ne peut être modifié(e)",
            $resource
        );
    }
}
