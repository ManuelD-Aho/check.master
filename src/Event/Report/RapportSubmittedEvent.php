<?php

declare(strict_types=1);

namespace App\Event\Report;

final class RapportSubmittedEvent
{
    private int $rapportId;
    private int $etudiantId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, int $etudiantId)
    {
        $this->rapportId = $rapportId;
        $this->etudiantId = $etudiantId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getRapportId(): int
    {
        return $this->rapportId;
    }

    public function getEtudiantId(): int
    {
        return $this->etudiantId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
