<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\CompteRenduRapport;
use App\Repository\AbstractRepository;

class CompteRenduRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CompteRenduRapport::class;
    }

    public function findByCompteRendu(int $compteRenduId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompteRenduRapport::class, 'c')
            ->join('c.compteRendu', 'cr')
            ->where('cr.idCompteRendu = :compteRenduId')
            ->setParameter('compteRenduId', $compteRenduId)
            ->getQuery()
            ->getResult();
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompteRenduRapport::class, 'c')
            ->join('c.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }
}
