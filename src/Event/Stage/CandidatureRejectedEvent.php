<?php

declare(strict_types=1);

namespace App\Event\Stage;

final class CandidatureRejectedEvent
{
    private int $candidatureId;
    private int $validateurId;
    private string $motif;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $candidatureId, int $validateurId, string $motif)
    {
        $this->candidatureId = $candidatureId;
        $this->validateurId = $validateurId;
        $this->motif = $motif;
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

    public function getMotif(): string
    {
        return $this->motif;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
