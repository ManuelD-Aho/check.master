<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Signature
 * 
 * Lancée lors d'erreurs liées aux signatures électroniques.
 */
class SignatureException extends AppException
{
    protected int $httpCode = 400;
    protected string $errorCode = 'SIGNATURE_ERROR';

    private string $documentType = '';
    private int $documentId = 0;
    private int $signataire = 0;

    /**
     * @param string $message Message d'erreur
     * @param string $documentType Type de document
     * @param int $documentId ID du document
     * @param int $signataire ID du signataire
     */
    public function __construct(
        string $message = 'Erreur de signature',
        string $documentType = '',
        int $documentId = 0,
        int $signataire = 0
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
        if ($signataire > 0) {
            $details['signataire'] = $signataire;
            $this->signataire = $signataire;
        }

        parent::__construct($message, 400, 'SIGNATURE_ERROR', $details);
    }

    /**
     * Crée une exception pour OTP invalide
     */
    public static function invalidOtp(): self
    {
        $e = new self('Code OTP invalide ou expiré');
        $e->errorCode = 'INVALID_OTP';
        return $e;
    }

    /**
     * Crée une exception pour trop de tentatives OTP
     */
    public static function tooManyOtpAttempts(): self
    {
        $e = new self('Trop de tentatives. Veuillez demander un nouveau code OTP.');
        $e->errorCode = 'TOO_MANY_OTP_ATTEMPTS';
        $e->httpCode = 429;
        return $e;
    }

    /**
     * Crée une exception pour signataire non autorisé
     */
    public static function unauthorized(int $signataire, string $documentType): self
    {
        return new self(
            "Vous n'êtes pas autorisé à signer ce document",
            $documentType,
            0,
            $signataire
        );
    }

    /**
     * Crée une exception pour document déjà signé
     */
    public static function alreadySigned(string $documentType, int $documentId, int $signataire): self
    {
        return new self(
            "Ce document a déjà été signé par vous",
            $documentType,
            $documentId,
            $signataire
        );
    }

    /**
     * Crée une exception pour signature hors délai
     */
    public static function expired(string $documentType, int $documentId): self
    {
        return new self(
            "Le délai de signature pour ce document est expiré",
            $documentType,
            $documentId
        );
    }

    /**
     * Crée une exception pour signature préalable requise
     */
    public static function previousSignatureRequired(string $documentType, int $documentId, string $signatairePrecedent): self
    {
        return new self(
            "La signature de '{$signatairePrecedent}' est requise avant la vôtre",
            $documentType,
            $documentId
        );
    }

    /**
     * Crée une exception pour image de signature manquante
     */
    public static function signatureImageMissing(int $signataire): self
    {
        return new self(
            "Aucune image de signature n'est configurée pour votre compte",
            '',
            0,
            $signataire
        );
    }

    /**
     * Crée une exception pour échec de vérification
     */
    public static function verificationFailed(string $documentType, int $documentId): self
    {
        return new self(
            "La vérification de la signature a échoué",
            $documentType,
            $documentId
        );
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

    /**
     * Retourne l'ID du signataire
     */
    public function getSignataire(): int
    {
        return $this->signataire;
    }
}
