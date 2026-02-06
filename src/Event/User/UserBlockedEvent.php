<?php

declare(strict_types=1);

namespace App\Event\User;

final class UserBlockedEvent
{
    private int $userId;
    private string $reason;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $userId, string $reason)
    {
        $this->userId = $userId;
        $this->reason = $reason;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
