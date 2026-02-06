<?php

declare(strict_types=1);

namespace App\Event\Stage;

final class CandidatureSubmittedEvent
{
    private int $candidatureId;
    private int $etudiantId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $candidatureId, int $etudiantId)
    {
        $this->candidatureId = $candidatureId;
        $this->etudiantId = $etudiantId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getCandidatureId(): int
    {
        return $this->candidatureId;
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
