<?php

declare(strict_types=1);

namespace App\Event\Soutenance;

final class DeliberationCompletedEvent
{
    private int $soutenanceId;
    private float $moyenneFinale;
    private string $decision;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $soutenanceId, float $moyenneFinale, string $decision)
    {
        $this->soutenanceId = $soutenanceId;
        $this->moyenneFinale = $moyenneFinale;
        $this->decision = $decision;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getSoutenanceId(): int
    {
        return $this->soutenanceId;
    }

    public function getMoyenneFinale(): float
    {
        return $this->moyenneFinale;
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
