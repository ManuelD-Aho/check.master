<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\EvaluationRapport;
use App\Repository\AbstractRepository;

class EvaluationRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return EvaluationRapport::class;
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }

    public function findByEvaluateur(int $evaluateurId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.evaluateur', 'u')
            ->where('u.idUtilisateur = :evaluateurId')
            ->setParameter('evaluateurId', $evaluateurId)
            ->getQuery()
            ->getResult();
    }

    public function findPendingEvaluations(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.rapport', 'r')
            ->join('r.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->andWhere('e.decisionEvaluation IS NULL')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByCycle(int $rapportId, int $cycle): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->andWhere('e.numeroCycle = :cycle')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('cycle', $cycle)
            ->getQuery()
            ->getResult();
    }
}
