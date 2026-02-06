<?php

declare(strict_types=1);

namespace App\Event\Commission;

final class VoteCompleteEvent
{
    private int $rapportId;
    private bool $accepted;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, bool $accepted)
    {
        $this->rapportId = $rapportId;
        $this->accepted = $accepted;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getRapportId(): int
    {
        return $this->rapportId;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
