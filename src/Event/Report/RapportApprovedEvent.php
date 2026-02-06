<?php

declare(strict_types=1);

namespace App\Event\Report;

final class RapportApprovedEvent
{
    private int $rapportId;
    private int $verificateurId;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, int $verificateurId)
    {
        $this->rapportId = $rapportId;
        $this->verificateurId = $verificateurId;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getRapportId(): int
    {
        return $this->rapportId;
    }

    public function getVerificateurId(): int
    {
        return $this->verificateurId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
