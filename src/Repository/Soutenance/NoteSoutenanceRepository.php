<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\NoteSoutenance;
use App\Repository\AbstractRepository;

class NoteSoutenanceRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return NoteSoutenance::class;
    }

    public function findBySoutenance(int $soutenanceId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(NoteSoutenance::class, 'n')
            ->join('n.soutenance', 's')
            ->where('s.idSoutenance = :soutenanceId')
            ->setParameter('soutenanceId', $soutenanceId)
            ->getQuery()
            ->getResult();
    }

    public function findByCritere(int $critereId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(NoteSoutenance::class, 'n')
            ->join('n.critere', 'c')
            ->where('c.idCritere = :critereId')
            ->setParameter('critereId', $critereId)
            ->getQuery()
            ->getResult();
    }

    public function calculateTotalNote(int $soutenanceId): float
    {
        $total = $this->entityManager->createQueryBuilder()
            ->select('SUM(n.note)')
            ->from(NoteSoutenance::class, 'n')
            ->join('n.soutenance', 's')
            ->where('s.idSoutenance = :soutenanceId')
            ->setParameter('soutenanceId', $soutenanceId)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($total ?? 0.0);
    }
}
