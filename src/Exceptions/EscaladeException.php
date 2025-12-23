<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Escalade
 * 
 * Lancée lors d'erreurs liées aux escalades SLA ou commission.
 * Code HTTP: 422 Unprocessable Entity
 */
class EscaladeException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'ESCALADE_ERROR';

    private int $niveau = 0;
    private int $entiteId = 0;

    /**
     * @param string $message Message d'erreur
     * @param int $niveau Niveau d'escalade concerné
     * @param int $entiteId ID de l'entité concernée
     */
    public function __construct(
        string $message = 'Erreur lors de l\'escalade',
        int $niveau = 0,
        int $entiteId = 0
    ) {
        $details = [];
        if ($niveau > 0) {
            $details['niveau'] = $niveau;
            $this->niveau = $niveau;
        }
        if ($entiteId > 0) {
            $details['entite_id'] = $entiteId;
            $this->entiteId = $entiteId;
        }

        parent::__construct($message, 422, 'ESCALADE_ERROR', $details);
    }

    /**
     * Crée une exception pour niveau d'escalade invalide
     */
    public static function niveauInvalide(int $niveau): self
    {
        return new self("Niveau d'escalade invalide: {$niveau}", $niveau);
    }

    /**
     * Crée une exception pour escalade déjà en cours
     */
    public static function dejaEnCours(int $entiteId): self
    {
        return new self("Une escalade est déjà en cours pour l'entité #{$entiteId}", 0, $entiteId);
    }

    /**
     * Crée une exception pour escalade non autorisée
     */
    public static function nonAutorisee(string $raison = ''): self
    {
        $message = 'Escalade non autorisée';
        if ($raison !== '') {
            $message .= ": {$raison}";
        }
        return new self($message);
    }

    /**
     * Crée une exception pour résolution impossible
     */
    public static function resolutionImpossible(string $raison): self
    {
        return new self("Impossible de résoudre l'escalade: {$raison}");
    }

    /**
     * Crée une exception pour niveau maximum atteint
     */
    public static function niveauMaxAtteint(int $niveau): self
    {
        return new self("Niveau d'escalade maximum atteint: {$niveau}", $niveau);
    }

    /**
     * Retourne le niveau d'escalade
     */
    public function getNiveau(): int
    {
        return $this->niveau;
    }

    /**
     * Retourne l'ID de l'entité
     */
    public function getEntiteId(): int
    {
        return $this->entiteId;
    }
}
