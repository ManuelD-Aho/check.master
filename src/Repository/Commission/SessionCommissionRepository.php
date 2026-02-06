<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\SessionCommission;
use App\Entity\Commission\StatutSession;
use App\Repository\AbstractRepository;

class SessionCommissionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return SessionCommission::class;
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(SessionCommission::class, 's')
            ->join('s.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findOpen(): array
    {
        return $this->findBy(['statutSession' => StatutSession::Ouverte]);
    }

    public function findByMois(int $mois, int $annee): ?SessionCommission
    {
        return $this->findOneBy(['moisSession' => $mois, 'anneeSession' => $annee]);
    }

    public function findCurrent(): ?SessionCommission
    {
        $today = new \DateTimeImmutable();

        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(SessionCommission::class, 's')
            ->where('s.dateDebut <= :today')
            ->andWhere('s.dateFin >= :today')
            ->andWhere('s.statutSession = :statut')
            ->setParameter('today', $today)
            ->setParameter('statut', StatutSession::Ouverte)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
