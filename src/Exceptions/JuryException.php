<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Jury
 * 
 * Lancée lors d'erreurs liées à la gestion des jurys de soutenance.
 */
class JuryException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'JURY_ERROR';

    private int $juryId = 0;
    private int $soutenanceId = 0;

    /**
     * @param string $message Message d'erreur
     * @param int $juryId ID du jury
     * @param int $soutenanceId ID de la soutenance
     */
    public function __construct(
        string $message = 'Erreur de jury',
        int $juryId = 0,
        int $soutenanceId = 0
    ) {
        $details = [];
        
        if ($juryId > 0) {
            $details['jury_id'] = $juryId;
            $this->juryId = $juryId;
        }
        if ($soutenanceId > 0) {
            $details['soutenance_id'] = $soutenanceId;
            $this->soutenanceId = $soutenanceId;
        }

        parent::__construct($message, 422, 'JURY_ERROR', $details);
    }

    /**
     * Crée une exception pour jury incomplet
     */
    public static function incomplete(int $juryId, int $membresActuels, int $membresRequis): self
    {
        return new self(
            "Le jury #{$juryId} est incomplet. Membres: {$membresActuels}/{$membresRequis}",
            $juryId
        );
    }

    /**
     * Crée une exception pour président non désigné
     */
    public static function noPresident(int $juryId): self
    {
        return new self(
            "Aucun président n'a été désigné pour le jury #{$juryId}",
            $juryId
        );
    }

    /**
     * Crée une exception pour conflit d'horaire
     */
    public static function scheduleConflict(int $membreId, string $dateHeure): self
    {
        return new self(
            "Le membre #{$membreId} a un conflit d'horaire le {$dateHeure}"
        );
    }

    /**
     * Crée une exception pour encadreur dans le jury
     */
    public static function supervisorInJury(int $juryId, int $enseignantId): self
    {
        return new self(
            "L'encadreur ne peut pas être membre votant du jury",
            $juryId
        );
    }

    /**
     * Crée une exception pour membre déjà présent
     */
    public static function memberAlreadyExists(int $juryId, int $membreId): self
    {
        return new self(
            "Ce membre fait déjà partie du jury",
            $juryId
        );
    }

    /**
     * Crée une exception pour composition figée
     */
    public static function compositionLocked(int $juryId): self
    {
        return new self(
            "La composition du jury #{$juryId} est figée et ne peut plus être modifiée",
            $juryId
        );
    }

    /**
     * Crée une exception pour grade insuffisant
     */
    public static function insufficientRank(int $membreId, string $role, string $gradeRequis): self
    {
        return new self(
            "Le membre #{$membreId} n'a pas le grade requis ({$gradeRequis}) pour le rôle de {$role}"
        );
    }

    /**
     * Crée une exception pour absence non justifiée
     */
    public static function unjustifiedAbsence(int $juryId, int $membreId): self
    {
        return new self(
            "Absence non justifiée du membre #{$membreId} dans le jury #{$juryId}",
            $juryId
        );
    }

    /**
     * Crée une exception pour notes incomplètes
     */
    public static function incompleteGrades(int $juryId, int $soutenanceId): self
    {
        return new self(
            "Tous les membres du jury n'ont pas encore saisi leurs notes",
            $juryId,
            $soutenanceId
        );
    }

    /**
     * Retourne l'ID du jury
     */
    public function getJuryId(): int
    {
        return $this->juryId;
    }

    /**
     * Retourne l'ID de la soutenance
     */
    public function getSoutenanceId(): int
    {
        return $this->soutenanceId;
    }
}
