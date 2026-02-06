<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\DecisionJury;
use App\Entity\Soutenance\ResultatFinal;
use App\Repository\AbstractRepository;

class ResultatFinalRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return ResultatFinal::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(ResultatFinal::class, 'r')
            ->join('r.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(ResultatFinal::class, 'r')
            ->join('r.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findBySoutenance(int $soutenanceId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(ResultatFinal::class, 'r')
            ->join('r.soutenance', 's')
            ->where('s.idSoutenance = :soutenanceId')
            ->setParameter('soutenanceId', $soutenanceId)
            ->getQuery()
            ->getResult();
    }

    public function findByDecision(string $decision): array
    {
        return $this->findBy(['decisionJury' => DecisionJury::from($decision)]);
    }

    public function findValidated(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(ResultatFinal::class, 'r')
            ->join('r.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->andWhere('r.valide = :valide')
            ->setParameter('anneeId', $anneeId)
            ->setParameter('valide', true)
            ->getQuery()
            ->getResult();
    }
}
