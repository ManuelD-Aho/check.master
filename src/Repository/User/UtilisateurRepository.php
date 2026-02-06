<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\Utilisateur;
use App\Entity\User\UtilisateurStatut;
use App\Repository\AbstractRepository;

class UtilisateurRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Utilisateur::class;
    }

    public function findByLogin(string $login): ?Utilisateur
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['loginUtilisateur' => $login]);
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['emailUtilisateur' => $email]);
    }

    public function findActiveUsers(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->where('u.statutUtilisateur = :statut')
            ->setParameter('statut', UtilisateurStatut::Actif)
            ->getQuery()
            ->getResult();
    }

    public function findByGroupe(int $groupeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->innerJoin('u.groupeUtilisateur', 'g')
            ->where('g.idGroupeUtilisateur = :groupeId')
            ->setParameter('groupeId', $groupeId)
            ->getQuery()
            ->getResult();
    }

    public function findByStatut(string $statut): array
    {
        $statutEnum = UtilisateurStatut::from($statut);

        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->where('u.statutUtilisateur = :statut')
            ->setParameter('statut', $statutEnum)
            ->getQuery()
            ->getResult();
    }
}
