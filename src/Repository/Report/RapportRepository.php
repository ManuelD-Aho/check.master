<?php

declare(strict_types=1);

namespace App\Repository\Report;

use App\Entity\Report\Rapport;
use App\Entity\Report\StatutRapport;
use App\Repository\AbstractRepository;

class RapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Rapport::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Rapport::class, 'r')
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
            ->from(Rapport::class, 'r')
            ->join('r.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByEtudiantAndAnnee(string $matricule, int $anneeId): ?Rapport
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Rapport::class, 'r')
            ->join('r.etudiant', 'e')
            ->join('r.anneeAcademique', 'a')
            ->where('e.matriculeEtudiant = :matricule')
            ->andWhere('a.idAnneeAcademique = :anneeId')
            ->setParameter('matricule', $matricule)
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['statutRapport' => StatutRapport::from($statut)]);
    }

    public function findReadyForCommission(): array
    {
        return $this->findBy(['statutRapport' => StatutRapport::EN_COMMISSION]);
    }

    public function findApproved(): array
    {
        return $this->findBy(['statutRapport' => StatutRapport::APPROUVE]);
    }
}
