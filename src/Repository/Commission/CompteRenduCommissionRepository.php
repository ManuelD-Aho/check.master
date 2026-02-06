<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\CompteRenduCommission;
use App\Entity\Commission\StatutPv;
use App\Repository\AbstractRepository;

class CompteRenduCommissionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CompteRenduCommission::class;
    }

    public function findBySession(int $sessionId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompteRenduCommission::class, 'c')
            ->join('c.session', 's')
            ->where('s.idSession = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery()
            ->getResult();
    }

    public function findByNumero(string $numero): ?CompteRenduCommission
    {
        return $this->findOneBy(['numeroPv' => $numero]);
    }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['statutPv' => StatutPv::from($statut)]);
    }
}
