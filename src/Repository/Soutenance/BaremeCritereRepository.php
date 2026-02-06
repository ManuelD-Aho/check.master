<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\BaremeCritere;
use App\Repository\AbstractRepository;

class BaremeCritereRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return BaremeCritere::class;
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('b')
            ->from(BaremeCritere::class, 'b')
            ->join('b.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByCritere(int $critereId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('b')
            ->from(BaremeCritere::class, 'b')
            ->join('b.critere', 'c')
            ->where('c.idCritere = :critereId')
            ->setParameter('critereId', $critereId)
            ->getQuery()
            ->getResult();
    }
}
