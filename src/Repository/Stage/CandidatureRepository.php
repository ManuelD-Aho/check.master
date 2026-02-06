<?php

declare(strict_types=1);

namespace App\Repository\Stage;

use App\Entity\Stage\Candidature;
use App\Entity\Stage\StatutCandidature;
use App\Repository\AbstractRepository;

class CandidatureRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Candidature::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Candidature::class, 'c')
            ->join('c.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Candidature::class, 'c')
            ->join('c.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByEtudiantAndAnnee(string $matricule, int $anneeId): ?Candidature
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Candidature::class, 'c')
            ->join('c.etudiant', 'e')
            ->join('c.anneeAcademique', 'a')
            ->where('e.matriculeEtudiant = :matricule')
            ->andWhere('a.idAnneeAcademique = :anneeId')
            ->setParameter('matricule', $matricule)
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['statutCandidature' => StatutCandidature::from($statut)]);
    }

    public function findPendingValidation(): array
    {
        return $this->findBy(['statutCandidature' => StatutCandidature::Soumise]);
    }
}
