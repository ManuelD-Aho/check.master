<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\Salle;
use App\Entity\Soutenance\Soutenance;
use App\Repository\AbstractRepository;

class SalleRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Salle::class;
    }

    public function findByCode(string $code): ?Salle
    {
        return $this->findOneBy(['codeSalle' => $code]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findAvailable(\DateTimeInterface $date, \DateTimeInterface $heureDebut, \DateTimeInterface $heureFin): array
    {
        $subQuery = $this->entityManager->createQueryBuilder()
            ->select('s2.idSalle')
            ->from(Soutenance::class, 'so')
            ->join('so.salle', 's2')
            ->where('so.dateSoutenance = :date')
            ->andWhere('so.heureDebut < :heureFin')
            ->andWhere('(so.heureFin IS NULL OR so.heureFin > :heureDebut)')
            ->getDQL();

        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Salle::class, 's')
            ->where('s.actif = :actif')
            ->andWhere('s.idSalle NOT IN (' . $subQuery . ')')
            ->setParameter('actif', true)
            ->setParameter('date', $date)
            ->setParameter('heureDebut', $heureDebut)
            ->setParameter('heureFin', $heureFin)
            ->getQuery()
            ->getResult();
    }
}
