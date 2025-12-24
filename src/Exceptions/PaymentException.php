<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Payment (Paiement)
 * 
 * Lancée lors d'erreurs liées aux paiements.
 */
class PaymentException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'PAYMENT_ERROR';

    private int $paiementId = 0;
    private float $montant = 0.0;
    private string $reference = '';

    /**
     * @param string $message Message d'erreur
     * @param int $paiementId ID du paiement
     * @param float $montant Montant concerné
     * @param string $reference Référence de paiement
     */
    public function __construct(
        string $message = 'Erreur de paiement',
        int $paiementId = 0,
        float $montant = 0.0,
        string $reference = ''
    ) {
        $details = [];
        
        if ($paiementId > 0) {
            $details['paiement_id'] = $paiementId;
            $this->paiementId = $paiementId;
        }
        if ($montant > 0) {
            $details['montant'] = $montant;
            $this->montant = $montant;
        }
        if ($reference !== '') {
            $details['reference'] = $reference;
            $this->reference = $reference;
        }

        parent::__construct($message, 422, 'PAYMENT_ERROR', $details);
    }

    /**
     * Crée une exception pour montant invalide
     */
    public static function invalidAmount(float $montant, float $montantAttendu): self
    {
        return new self(
            "Montant invalide. Reçu: {$montant} FCFA, Attendu: {$montantAttendu} FCFA",
            0,
            $montant
        );
    }

    /**
     * Crée une exception pour paiement déjà validé
     */
    public static function alreadyValidated(int $paiementId, string $reference): self
    {
        return new self(
            "Ce paiement a déjà été validé",
            $paiementId,
            0.0,
            $reference
        );
    }

    /**
     * Crée une exception pour doublon de paiement
     */
    public static function duplicate(string $reference): self
    {
        return new self(
            "Un paiement avec cette référence existe déjà: {$reference}",
            0,
            0.0,
            $reference
        );
    }

    /**
     * Crée une exception pour paiement annulé
     */
    public static function cancelled(int $paiementId): self
    {
        return new self(
            "Ce paiement a été annulé",
            $paiementId
        );
    }

    /**
     * Crée une exception pour mode de paiement invalide
     */
    public static function invalidPaymentMethod(string $method): self
    {
        return new self("Mode de paiement non supporté: {$method}");
    }

    /**
     * Crée une exception pour exonération invalide
     */
    public static function invalidExoneration(int $etudiantId, string $reason = ''): self
    {
        $message = "L'étudiant #{$etudiantId} n'est pas éligible à une exonération";
        if ($reason !== '') {
            $message .= ": {$reason}";
        }
        return new self($message);
    }

    /**
     * Crée une exception pour remboursement impossible
     */
    public static function refundNotAllowed(int $paiementId, string $reason = ''): self
    {
        $message = "Remboursement impossible pour le paiement #{$paiementId}";
        if ($reason !== '') {
            $message .= ": {$reason}";
        }
        return new self($message, $paiementId);
    }

    /**
     * Crée une exception pour délai de paiement dépassé
     */
    public static function deadlineExceeded(int $etudiantId, string $deadline): self
    {
        return new self(
            "Le délai de paiement est dépassé (échéance: {$deadline}). Des pénalités peuvent s'appliquer."
        );
    }

    /**
     * Crée une exception pour reçu non disponible
     */
    public static function receiptNotAvailable(int $paiementId): self
    {
        return new self(
            "Le reçu n'est pas encore disponible pour ce paiement",
            $paiementId
        );
    }

    /**
     * Retourne l'ID du paiement
     */
    public function getPaiementId(): int
    {
        return $this->paiementId;
    }

    /**
     * Retourne le montant
     */
    public function getMontant(): float
    {
        return $this->montant;
    }

    /**
     * Retourne la référence
     */
    public function getReference(): string
    {
        return $this->reference;
    }
}
