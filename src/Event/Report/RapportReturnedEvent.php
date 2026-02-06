<?php

declare(strict_types=1);

namespace App\Event\Report;

final class RapportReturnedEvent
{
    private int $rapportId;
    private int $verificateurId;
    private string $motif;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $rapportId, int $verificateurId, string $motif)
    {
        $this->rapportId = $rapportId;
        $this->verificateurId = $verificateurId;
        $this->motif = $motif;
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

    public function getMotif(): string
    {
        return $this->motif;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
