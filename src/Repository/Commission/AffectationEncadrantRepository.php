<?php

declare(strict_types=1);

namespace App\Repository\Commission;

use App\Entity\Commission\AffectationEncadrant;
use App\Entity\Commission\RoleEncadrement;
use App\Repository\AbstractRepository;

class AffectationEncadrantRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AffectationEncadrant::class;
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AffectationEncadrant::class, 'a')
            ->join('a.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }

    public function findByEnseignant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AffectationEncadrant::class, 'a')
            ->join('a.enseignant', 'e')
            ->where('e.matriculeEnseignant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findDirecteurMemoire(int $rapportId): ?AffectationEncadrant
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AffectationEncadrant::class, 'a')
            ->join('a.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->andWhere('a.roleEncadrement = :role')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('role', RoleEncadrement::DirecteurMemoire)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findEncadreurPedagogique(int $rapportId): ?AffectationEncadrant
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AffectationEncadrant::class, 'a')
            ->join('a.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->andWhere('a.roleEncadrement = :role')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('role', RoleEncadrement::EncadreurPedagogique)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
