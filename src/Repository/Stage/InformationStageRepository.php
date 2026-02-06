<?php

declare(strict_types=1);

namespace App\Repository\Stage;

use App\Entity\Stage\InformationStage;
use App\Repository\AbstractRepository;

class InformationStageRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return InformationStage::class;
    }

    public function findByCandidature(int $candidatureId): ?InformationStage
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from(InformationStage::class, 'i')
            ->join('i.candidature', 'c')
            ->where('c.idCandidature = :candidatureId')
            ->setParameter('candidatureId', $candidatureId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEntreprise(int $entrepriseId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from(InformationStage::class, 'i')
            ->join('i.entreprise', 'e')
            ->where('e.idEntreprise = :entrepriseId')
            ->setParameter('entrepriseId', $entrepriseId)
            ->getQuery()
            ->getResult();
    }
}
