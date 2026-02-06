<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\AptitudeSoutenance;
use App\Repository\AbstractRepository;

class AptitudeSoutenanceRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AptitudeSoutenance::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.anneeAcademique', 'an')
            ->where('an.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByEncadreur(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.encadreur', 'e')
            ->where('e.matriculeEnseignant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findAptes(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.anneeAcademique', 'an')
            ->where('an.idAnneeAcademique = :anneeId')
            ->andWhere('a.estApte = :estApte')
            ->setParameter('anneeId', $anneeId)
            ->setParameter('estApte', true)
            ->getQuery()
            ->getResult();
    }
}
