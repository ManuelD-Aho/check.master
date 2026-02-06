<?php

declare(strict_types=1);

namespace App\Event\Commission;

final class VoteSubmittedEvent
{
    private int $rapportId;
    private int $membreId;
    private string $decision;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, int $membreId, string $decision)
    {
        $this->rapportId = $rapportId;
        $this->membreId = $membreId;
        $this->decision = $decision;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getRapportId(): int
    {
        return $this->rapportId;
    }

    public function getMembreId(): int
    {
        return $this->membreId;
    }

    public function getDecision(): string
    {
        return $this->decision;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
