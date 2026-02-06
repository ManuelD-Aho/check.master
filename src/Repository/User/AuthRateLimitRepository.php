<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\AuthRateLimit;
use App\Repository\AbstractRepository;

class AuthRateLimitRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AuthRateLimit::class;
    }

    public function findByIpAndAction(string $ip, string $action): ?AuthRateLimit
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['adresseIp' => $ip, 'action' => $action]);
    }

    public function cleanExpired(): void
    {
        $now = new \DateTimeImmutable();

        $this->entityManager->createQueryBuilder()
            ->delete($this->getEntityClass(), 'a')
            ->where('a.bloqueJusqu IS NOT NULL')
            ->andWhere('a.bloqueJusqu < :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->execute();
    }
}
