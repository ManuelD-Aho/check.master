<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Archive
 * 
 * Lancée lors d'erreurs liées à l'archivage de documents.
 */
class ArchiveException extends AppException
{
    protected int $httpCode = 500;
    protected string $errorCode = 'ARCHIVE_ERROR';

    private string $documentType = '';
    private int $documentId = 0;

    /**
     * @param string $message Message d'erreur
     * @param string $documentType Type de document
     * @param int $documentId ID du document
     */
    public function __construct(
        string $message = 'Erreur lors de l\'archivage',
        string $documentType = '',
        int $documentId = 0
    ) {
        $details = [];
        
        if ($documentType !== '') {
            $details['document_type'] = $documentType;
            $this->documentType = $documentType;
        }
        if ($documentId > 0) {
            $details['document_id'] = $documentId;
            $this->documentId = $documentId;
        }

        parent::__construct($message, 500, 'ARCHIVE_ERROR', $details);
    }

    /**
     * Crée une exception pour document non trouvé
     */
    public static function documentNotFound(string $documentType, int $documentId): self
    {
        return new self(
            "Document à archiver non trouvé: {$documentType} #{$documentId}",
            $documentType,
            $documentId
        );
    }

    /**
     * Crée une exception pour échec d'archivage
     */
    public static function archiveFailed(string $documentType, int $documentId, string $reason = ''): self
    {
        $message = "Échec de l'archivage du document {$documentType} #{$documentId}";
        if ($reason !== '') {
            $message .= ": {$reason}";
        }
        
        return new self($message, $documentType, $documentId);
    }

    /**
     * Crée une exception pour erreur d'intégrité
     */
    public static function integrityError(int $archiveId, string $expectedHash, string $actualHash): self
    {
        return new self(
            "Erreur d'intégrité détectée pour l'archive #{$archiveId}. " .
            "Hash attendu: {$expectedHash}, Hash actuel: {$actualHash}"
        );
    }

    /**
     * Crée une exception pour stockage plein
     */
    public static function storageFull(): self
    {
        return new self("Espace de stockage des archives insuffisant");
    }

    /**
     * Crée une exception pour document déjà archivé
     */
    public static function alreadyArchived(string $documentType, int $documentId): self
    {
        return new self(
            "Le document {$documentType} #{$documentId} est déjà archivé",
            $documentType,
            $documentId
        );
    }

    /**
     * Crée une exception pour restauration impossible
     */
    public static function cannotRestore(int $archiveId, string $reason = ''): self
    {
        $message = "Impossible de restaurer l'archive #{$archiveId}";
        if ($reason !== '') {
            $message .= ": {$reason}";
        }
        return new self($message);
    }

    /**
     * Retourne le type de document
     */
    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    /**
     * Retourne l'ID du document
     */
    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
