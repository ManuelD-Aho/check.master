<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Audit
 * 
 * Lancée lors d'erreurs liées à la journalisation d'audit.
 */
class AuditException extends AppException
{
    protected int $httpCode = 500;
    protected string $errorCode = 'AUDIT_ERROR';

    /**
     * @param string $message Message d'erreur
     * @param string $errorCode Code d'erreur spécifique
     */
    public function __construct(
        string $message = 'Erreur de journalisation audit',
        string $errorCode = 'AUDIT_ERROR'
    ) {
        parent::__construct($message, 500, $errorCode);
    }

    /**
     * Crée une exception pour échec d'écriture
     */
    public static function writeFailed(string $action, string $reason = ''): self
    {
        $message = "Échec de l'enregistrement de l'action audit: {$action}";
        if ($reason !== '') {
            $message .= " - {$reason}";
        }
        return new self($message, 'AUDIT_WRITE_FAILED');
    }

    /**
     * Crée une exception pour données d'audit invalides
     */
    public static function invalidData(string $field): self
    {
        return new self(
            "Données d'audit invalides: champ '{$field}' manquant ou invalide",
            'AUDIT_INVALID_DATA'
        );
    }

    /**
     * Crée une exception pour table audit inaccessible
     */
    public static function tableUnavailable(): self
    {
        return new self(
            "La table d'audit est temporairement inaccessible",
            'AUDIT_TABLE_UNAVAILABLE'
        );
    }

    /**
     * Crée une exception pour erreur de serialisation
     */
    public static function serializationError(): self
    {
        return new self(
            "Erreur lors de la sérialisation des données d'audit",
            'AUDIT_SERIALIZATION_ERROR'
        );
    }
}
