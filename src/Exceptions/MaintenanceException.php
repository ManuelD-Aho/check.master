<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Maintenance
 * 
 * Lancée lorsque le système est en mode maintenance.
 * Code HTTP: 503 Service Unavailable
 */
class MaintenanceException extends AppException
{
    protected int $httpCode = 503;
    protected string $errorCode = 'MAINTENANCE';

    private ?string $retourPrevu = null;

    /**
     * @param string $message Message d'erreur
     * @param string|null $retourPrevu Date/heure de retour prévue
     */
    public function __construct(string $message = 'Le système est en maintenance', ?string $retourPrevu = null)
    {
        $details = [];
        if ($retourPrevu !== null) {
            $details['retour_prevu'] = $retourPrevu;
            $this->retourPrevu = $retourPrevu;
        }

        parent::__construct($message, 503, 'MAINTENANCE', $details);
    }

    /**
     * Définit le retour prévu
     */
    public function setRetourPrevu(string $retour): self
    {
        $this->retourPrevu = $retour;
        $this->addDetail('retour_prevu', $retour);
        return $this;
    }

    /**
     * Retourne le retour prévu
     */
    public function getRetourPrevu(): ?string
    {
        return $this->retourPrevu;
    }

    /**
     * Crée une exception de maintenance planifiée
     */
    public static function planifiee(string $finPrevue): self
    {
        return new self(
            "Le système est en maintenance planifiée. Retour prévu: {$finPrevue}",
            $finPrevue
        );
    }

    /**
     * Crée une exception de maintenance urgente
     */
    public static function urgente(): self
    {
        return new self(
            "Le système est temporairement indisponible pour maintenance urgente. Veuillez réessayer plus tard."
        );
    }
}
