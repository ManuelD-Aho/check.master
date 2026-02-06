<?php

declare(strict_types=1);

namespace App\Repository\Report;

use App\Entity\Report\VersionRapport;
use App\Repository\AbstractRepository;

class VersionRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return VersionRapport::class;
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VersionRapport::class, 'v')
            ->join('v.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }

    public function findLatestVersion(int $rapportId): ?VersionRapport
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VersionRapport::class, 'v')
            ->join('v.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->orderBy('v.numeroVersion', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
