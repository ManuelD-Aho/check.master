<?php

declare(strict_types=1);

namespace App\Repository\Report;

use App\Entity\Report\ValidationRapport;
use App\Repository\AbstractRepository;

class ValidationRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return ValidationRapport::class;
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(ValidationRapport::class, 'v')
            ->join('v.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }

    public function findByValidateur(int $validateurId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(ValidationRapport::class, 'v')
            ->join('v.validateur', 'u')
            ->where('u.idUtilisateur = :validateurId')
            ->setParameter('validateurId', $validateurId)
            ->getQuery()
            ->getResult();
    }
}
