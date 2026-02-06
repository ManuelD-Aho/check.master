<?php

declare(strict_types=1);

namespace App\Event\User;

final class UserLoginEvent
{
    private int $userId;
    private string $ipAddress;
    private bool $successful;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $userId, string $ipAddress, bool $successful)
    {
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->successful = $successful;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
