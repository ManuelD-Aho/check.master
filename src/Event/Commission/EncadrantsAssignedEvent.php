<?php

declare(strict_types=1);

namespace App\Event\Commission;

final class EncadrantsAssignedEvent
{
    private int $rapportId;
    private int $directeurId;
    private int $encadreurId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, int $directeurId, int $encadreurId)
    {
        $this->rapportId = $rapportId;
        $this->directeurId = $directeurId;
        $this->encadreurId = $encadreurId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getRapportId(): int
    {
        return $this->rapportId;
    }

    public function getDirecteurId(): int
    {
        return $this->directeurId;
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
