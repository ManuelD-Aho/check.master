<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManager;

abstract class AbstractRepository
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function find(int $id): ?object
    {
        return $this->entityManager->find($this->getEntityClass(), $id);
    }

    public function findOneBy(array $criteria): ?object
    {
        return $this->entityManager->getRepository($this->getEntityClass())->findOneBy($criteria);
    }

    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->entityManager->getRepository($this->getEntityClass())->findBy(
            $criteria,
            $orderBy,
            $limit,
            $offset
        );
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository($this->getEntityClass())->findAll();
    }

    public function save(object $entity): void
    {
        $this->persist($entity);
        $this->flush();
    }

    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    abstract protected function getEntityClass(): string;
}
