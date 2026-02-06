<?php

declare(strict_types=1);

namespace App\Repository\System;

use App\Entity\System\AuditLog;
use App\Repository\AbstractRepository;

class AuditLogRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AuditLog::class;
    }

    public function findByUser(int $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from($this->getEntityClass(), 'a')
            ->leftJoin('a.utilisateur', 'u')
            ->where('u.idUtilisateur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByAction(string $action): array
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findBy(['action' => $action]);
    }

    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from($this->getEntityClass(), 'a')
            ->where('a.dateCreation BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findRecent(int $limit = 100): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from($this->getEntityClass(), 'a')
            ->orderBy('a.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
