<?php

declare(strict_types=1);

namespace App\Event\Student;

final class EtudiantCreatedEvent
{
    private int $etudiantId;
    private string $matricule;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $etudiantId, string $matricule)
    {
        $this->etudiantId = $etudiantId;
        $this->matricule = $matricule;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getEtudiantId(): int
    {
        return $this->etudiantId;
    }

    public function getMatricule(): string
    {
        return $this->matricule;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
