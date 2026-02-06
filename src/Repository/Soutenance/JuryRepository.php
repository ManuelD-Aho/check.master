<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\Jury;
use App\Entity\Soutenance\StatutJury;
use App\Repository\AbstractRepository;

class JuryRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Jury::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('j')
            ->from(Jury::class, 'j')
            ->join('j.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('j')
            ->from(Jury::class, 'j')
            ->join('j.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['statutJury' => StatutJury::from($statut)]);
    }

    public function findComplet(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('j')
            ->from(Jury::class, 'j')
            ->join('j.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->andWhere('j.statutJury = :statut')
            ->setParameter('anneeId', $anneeId)
            ->setParameter('statut', StatutJury::COMPLET)
            ->getQuery()
            ->getResult();
    }
}
