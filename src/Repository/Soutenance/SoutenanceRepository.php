<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\Soutenance;
use App\Entity\Soutenance\StatutSoutenance;
use App\Repository\AbstractRepository;

class SoutenanceRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Soutenance::class;
    }

    public function findByJury(int $juryId): ?Soutenance
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->join('s.jury', 'j')
            ->where('j.idJury = :juryId')
            ->setParameter('juryId', $juryId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->join('s.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findBySalle(int $salleId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->join('s.salle', 'sa')
            ->where('sa.idSalle = :salleId')
            ->setParameter('salleId', $salleId)
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTimeInterface $date): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->where('s.dateSoutenance = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['statutSoutenance' => StatutSoutenance::from($statut)]);
    }

    public function findUpcoming(int $limit = 10): array
    {
        $today = new \DateTimeImmutable();

        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->where('s.dateSoutenance >= :today')
            ->setParameter('today', $today)
            ->orderBy('s.dateSoutenance', 'ASC')
            ->addOrderBy('s.heureDebut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
