<?php

declare(strict_types=1);

namespace App\Event\Soutenance;

final class SoutenanceScheduledEvent
{
    private int $soutenanceId;
    private string $date;
    private string $heureDebut;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $soutenanceId, string $date, string $heureDebut)
    {
        $this->soutenanceId = $soutenanceId;
        $this->date = $date;
        $this->heureDebut = $heureDebut;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getSoutenanceId(): int
    {
        return $this->soutenanceId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
