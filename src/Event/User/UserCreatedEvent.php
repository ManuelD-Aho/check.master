<?php

declare(strict_types=1);

namespace App\Event\User;

final class UserCreatedEvent
{
    private int $userId;
    private string $login;
    private string $email;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $userId, string $login, string $email)
    {
        $this->userId = $userId;
        $this->login = $login;
        $this->email = $email;
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
