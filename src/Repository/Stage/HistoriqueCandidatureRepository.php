<?php

declare(strict_types=1);

namespace App\Repository\Stage;

use App\Entity\Stage\ActionHistorique;
use App\Entity\Stage\HistoriqueCandidature;
use App\Repository\AbstractRepository;

class HistoriqueCandidatureRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return HistoriqueCandidature::class;
    }

    public function findByCandidature(int $candidatureId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('h')
            ->from(HistoriqueCandidature::class, 'h')
            ->join('h.candidature', 'c')
            ->where('c.idCandidature = :candidatureId')
            ->setParameter('candidatureId', $candidatureId)
            ->getQuery()
            ->getResult();
    }

    public function findByAction(string $action): array
    {
        return $this->findBy(['action' => ActionHistorique::from($action)]);
    }
}
