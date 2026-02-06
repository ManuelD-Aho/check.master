<?php

declare(strict_types=1);

namespace App\Event\Stage;

final class CandidatureValidatedEvent
{
    private int $candidatureId;
    private int $validateurId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $candidatureId, int $validateurId)
    {
        $this->candidatureId = $candidatureId;
        $this->validateurId = $validateurId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getCandidatureId(): int
    {
        return $this->candidatureId;
    }

    public function getValidateurId(): int
    {
        return $this->validateurId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
