<?php

declare(strict_types=1);

namespace App\Event\Soutenance;

final class JuryComposedEvent
{
    private int $juryId;
    private int $soutenanceId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $juryId, int $soutenanceId)
    {
        $this->juryId = $juryId;
        $this->soutenanceId = $soutenanceId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getJuryId(): int
    {
        return $this->juryId;
    }

    public function getSoutenanceId(): int
    {
        return $this->soutenanceId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
