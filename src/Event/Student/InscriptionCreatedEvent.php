<?php

declare(strict_types=1);

namespace App\Event\Student;

final class InscriptionCreatedEvent
{
    private int $inscriptionId;
    private int $etudiantId;
    private int $anneeId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $inscriptionId, int $etudiantId, int $anneeId)
    {
        $this->inscriptionId = $inscriptionId;
        $this->etudiantId = $etudiantId;
        $this->anneeId = $anneeId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getInscriptionId(): int
    {
        return $this->inscriptionId;
    }

    public function getEtudiantId(): int
    {
        return $this->etudiantId;
    }

    public function getAnneeId(): int
    {
        return $this->anneeId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
