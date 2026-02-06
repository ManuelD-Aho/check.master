<?php

declare(strict_types=1);

namespace App\Event\Soutenance;

final class AptitudeValidatedEvent
{
    private int $etudiantId;
    private int $encadreurId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $etudiantId, int $encadreurId)
    {
        $this->etudiantId = $etudiantId;
        $this->encadreurId = $encadreurId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getEtudiantId(): int
    {
        return $this->etudiantId;
    }

    public function getEncadreurId(): int
    {
        return $this->encadreurId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
