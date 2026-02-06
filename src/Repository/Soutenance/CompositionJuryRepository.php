<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\CompositionJury;
use App\Repository\AbstractRepository;

class CompositionJuryRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CompositionJury::class;
    }

    public function findByJury(int $juryId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompositionJury::class, 'c')
            ->join('c.jury', 'j')
            ->where('j.idJury = :juryId')
            ->setParameter('juryId', $juryId)
            ->getQuery()
            ->getResult();
    }

    public function findByEnseignant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompositionJury::class, 'c')
            ->join('c.enseignant', 'e')
            ->where('e.matriculeEnseignant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByRole(int $roleId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompositionJury::class, 'c')
            ->join('c.roleJury', 'r')
            ->where('r.idRoleJury = :roleId')
            ->setParameter('roleId', $roleId)
            ->getQuery()
            ->getResult();
    }
}
