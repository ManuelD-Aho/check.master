<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\GroupeUtilisateur;
use App\Repository\AbstractRepository;

class GroupeUtilisateurRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return GroupeUtilisateur::class;
    }

    public function findByCode(string $code): ?GroupeUtilisateur
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeGroupe' => $code]);
    }

    public function findByType(int $typeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('g')
            ->from($this->getEntityClass(), 'g')
            ->innerJoin('g.typeUtilisateur', 't')
            ->where('t.idTypeUtilisateur = :typeId')
            ->setParameter('typeId', $typeId)
            ->getQuery()
            ->getResult();
    }

    public function findModifiableGroups(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('g')
            ->from($this->getEntityClass(), 'g')
            ->where('g.estModifiable = :modifiable')
            ->setParameter('modifiable', true)
            ->getQuery()
            ->getResult();
    }
}
