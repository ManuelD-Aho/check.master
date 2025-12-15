<?php

declare(strict_types=1);

namespace Src\Exceptions;

use PDOException;

/**
 * Exception Base de Données
 * 
 * Lancée pour les erreurs liées à la base de données.
 * Code HTTP: 500 Internal Server Error
 */
class DatabaseException extends AppException
{
    protected int $httpCode = 500;
    protected string $errorCode = 'DATABASE_ERROR';

    /**
     * @param string $message Message d'erreur (masqué en production)
     * @param PDOException|null $previous Exception PDO originale
     */
    public function __construct(
        string $message = 'Erreur de base de données',
        ?PDOException $previous = null
    ) {
        $details = [];

        // En mode debug, inclure plus d'informations
        if ($previous !== null && $this->isDebugMode()) {
            $details['sql_state'] = $previous->getCode();
            $details['driver_message'] = $previous->getMessage();
        }

        parent::__construct($message, 500, 'DATABASE_ERROR', $details, $previous);
    }

    /**
     * Vérifie si le mode debug est activé
     */
    private function isDebugMode(): bool
    {
        return defined('APP_DEBUG') && APP_DEBUG === true;
    }

    /**
     * Erreur de connexion
     */
    public static function connectionFailed(?PDOException $e = null): self
    {
        return new self('Impossible de se connecter à la base de données', $e);
    }

    /**
     * Erreur de requête
     */
    public static function queryFailed(string $query = '', ?PDOException $e = null): self
    {
        $exception = new self("Erreur lors de l'exécution de la requête", $e);
        if ($exception->isDebugMode() && $query !== '') {
            $exception->details['query'] = $query;
        }
        return $exception;
    }

    /**
     * Violation de contrainte d'intégrité
     */
    public static function integrityViolation(string $constraint = '', ?PDOException $e = null): self
    {
        $message = $constraint !== ''
            ? "Violation de contrainte d'intégrité: {$constraint}"
            : "Violation de contrainte d'intégrité";

        $exception = new self($message, $e);
        $exception->errorCode = 'INTEGRITY_VIOLATION';
        $exception->httpCode = 409;
        return $exception;
    }

    /**
     * Violation de clé unique (doublon)
     */
    public static function duplicateEntry(string $field = '', ?PDOException $e = null): self
    {
        $message = $field !== ''
            ? "Une entrée avec cette valeur pour '{$field}' existe déjà"
            : 'Une entrée avec ces valeurs existe déjà';

        $exception = new self($message, $e);
        $exception->errorCode = 'DUPLICATE_ENTRY';
        $exception->httpCode = 409;
        if ($field !== '') {
            $exception->details['field'] = $field;
        }
        return $exception;
    }

    /**
     * Transaction échouée
     */
    public static function transactionFailed(?PDOException $e = null): self
    {
        $exception = new self('La transaction a échoué', $e);
        $exception->errorCode = 'TRANSACTION_FAILED';
        return $exception;
    }

    /**
     * Deadlock détecté
     */
    public static function deadlock(?PDOException $e = null): self
    {
        $exception = new self('Deadlock détecté. Veuillez réessayer.', $e);
        $exception->errorCode = 'DEADLOCK';
        $exception->httpCode = 503;
        return $exception;
    }

    /**
     * Factory depuis une PDOException
     */
    public static function fromPDOException(PDOException $e): self
    {
        $sqlState = $e->getCode();

        // Mapper les codes SQL selon le type d'erreur
        return match (true) {
            str_starts_with((string) $sqlState, '23') => self::integrityViolation('', $e),
            $sqlState === '23000' => self::duplicateEntry('', $e),
            $sqlState === '40001' => self::deadlock($e),
            str_starts_with((string) $sqlState, '08') => self::connectionFailed($e),
            default => new self('Erreur de base de données', $e),
        };
    }
}
