<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Commission
 * 
 * Lancée lors d'erreurs liées aux sessions de commission.
 */
class CommissionException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'COMMISSION_ERROR';

    private int $sessionId = 0;
    private int $rapportId = 0;

    /**
     * @param string $message Message d'erreur
     * @param int $sessionId ID de la session de commission
     * @param int $rapportId ID du rapport concerné
     */
    public function __construct(
        string $message = 'Erreur de commission',
        int $sessionId = 0,
        int $rapportId = 0
    ) {
        $details = [];
        
        if ($sessionId > 0) {
            $details['session_id'] = $sessionId;
            $this->sessionId = $sessionId;
        }
        if ($rapportId > 0) {
            $details['rapport_id'] = $rapportId;
            $this->rapportId = $rapportId;
        }

        parent::__construct($message, 422, 'COMMISSION_ERROR', $details);
    }

    /**
     * Crée une exception pour quorum non atteint
     */
    public static function quorumNotReached(int $sessionId, int $present, int $required): self
    {
        return new self(
            "Quorum non atteint pour la session #{$sessionId}. Présents: {$present}, Requis: {$required}",
            $sessionId
        );
    }

    /**
     * Crée une exception pour session clôturée
     */
    public static function sessionClosed(int $sessionId): self
    {
        return new self(
            "La session de commission #{$sessionId} est clôturée",
            $sessionId
        );
    }

    /**
     * Crée une exception pour vote déjà effectué
     */
    public static function alreadyVoted(int $sessionId, int $rapportId, int $membreId): self
    {
        return new self(
            "Vous avez déjà voté pour ce rapport dans cette session",
            $sessionId,
            $rapportId
        );
    }

    /**
     * Crée une exception pour conflit d'intérêt
     */
    public static function conflictOfInterest(int $sessionId, int $rapportId, int $membreId): self
    {
        return new self(
            "Conflit d'intérêt détecté. Vous ne pouvez pas voter pour ce rapport.",
            $sessionId,
            $rapportId
        );
    }

    /**
     * Crée une exception pour tour de vote invalide
     */
    public static function invalidVotingRound(int $sessionId, int $currentRound, int $maxRounds): self
    {
        return new self(
            "Tour de vote invalide. Tour actuel: {$currentRound}, Maximum: {$maxRounds}",
            $sessionId
        );
    }

    /**
     * Crée une exception pour escalade au doyen déjà effectuée
     */
    public static function alreadyEscalated(int $sessionId, int $rapportId): self
    {
        return new self(
            "Ce rapport a déjà été escaladé au Doyen",
            $sessionId,
            $rapportId
        );
    }

    /**
     * Crée une exception pour membre non autorisé
     */
    public static function memberNotAuthorized(int $sessionId, int $membreId): self
    {
        return new self(
            "Vous n'êtes pas membre de cette session de commission",
            $sessionId
        );
    }

    /**
     * Crée une exception pour décision arbitrale en attente
     */
    public static function awaitingArbitration(int $rapportId): self
    {
        return new self(
            "Ce rapport est en attente de décision arbitrale du Doyen",
            0,
            $rapportId
        );
    }

    /**
     * Retourne l'ID de la session
     */
    public function getSessionId(): int
    {
        return $this->sessionId;
    }

    /**
     * Retourne l'ID du rapport
     */
    public function getRapportId(): int
    {
        return $this->rapportId;
    }
}
