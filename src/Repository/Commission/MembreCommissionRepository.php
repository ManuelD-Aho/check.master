<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\MembreCommission;
use App\Entity\Commission\RoleCommission;
use App\Repository\AbstractRepository;

class MembreCommissionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return MembreCommission::class;
    }

    public function findByAnnee(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(MembreCommission::class, 'm')
            ->join('m.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->getQuery()
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findPresident(int $anneeId): ?MembreCommission
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(MembreCommission::class, 'm')
            ->join('m.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->andWhere('m.roleCommission = :role')
            ->setParameter('anneeId', $anneeId)
            ->setParameter('role', RoleCommission::President)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(int $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(MembreCommission::class, 'm')
            ->join('m.utilisateur', 'u')
            ->where('u.idUtilisateur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
