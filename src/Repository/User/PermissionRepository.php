<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\Permission;
use App\Repository\AbstractRepository;

class PermissionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Permission::class;
    }

    public function findByGroupe(int $groupeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->innerJoin('p.groupeUtilisateur', 'g')
            ->where('g.idGroupeUtilisateur = :groupeId')
            ->setParameter('groupeId', $groupeId)
            ->getQuery()
            ->getResult();
    }

    public function findByFonctionnalite(int $fonctionnaliteId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->innerJoin('p.fonctionnalite', 'f')
            ->where('f.idFonctionnalite = :fonctionnaliteId')
            ->setParameter('fonctionnaliteId', $fonctionnaliteId)
            ->getQuery()
            ->getResult();
    }

    public function getPermissionsForUser(int $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->innerJoin('p.groupeUtilisateur', 'g')
            ->innerJoin('g.utilisateurs', 'u')
            ->where('u.idUtilisateur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
