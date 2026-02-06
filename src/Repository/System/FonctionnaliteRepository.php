<?php

declare(strict_types=1);

namespace App\Repository\System;

use App\Entity\System\Fonctionnalite;
use App\Repository\AbstractRepository;

class FonctionnaliteRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Fonctionnalite::class;
    }

    public function findByCode(string $code): ?Fonctionnalite
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeFonctionnalite' => $code]);
    }

    public function findByCategorie(int $categorieId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from($this->getEntityClass(), 'f')
            ->innerJoin('f.categorie', 'c')
            ->where('c.idCategorie = :categorieId')
            ->setParameter('categorieId', $categorieId)
            ->getQuery()
            ->getResult();
    }

    public function findActiveMenuItems(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from($this->getEntityClass(), 'f')
            ->where('f.actif = :actif')
            ->andWhere('f.estSousPage = :estSousPage')
            ->setParameter('actif', true)
            ->setParameter('estSousPage', false)
            ->orderBy('f.ordreAffichage', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSubPages(int $parentId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from($this->getEntityClass(), 'f')
            ->innerJoin('f.pageParente', 'p')
            ->where('p.idFonctionnalite = :parentId')
            ->andWhere('f.estSousPage = :estSousPage')
            ->setParameter('parentId', $parentId)
            ->setParameter('estSousPage', true)
            ->orderBy('f.ordreAffichage', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
