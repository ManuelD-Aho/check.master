<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\ElementConstitutif;
use App\Repository\AbstractRepository;

class ElementConstitutifRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return ElementConstitutif::class;
    }

    public function findByCode(string $code): array
    {
        return $this->findBy(['codeEcue' => $code]);
    }

    public function findByUniteEnseignement(int $idUe): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.uniteEnseignement', 'u')
            ->where('u.idUe = :idUe')
            ->setParameter('idUe', $idUe)
            ->getQuery()
            ->getResult();
    }

    public function findByEnseignant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.enseignant', 'en')
            ->where('en.matriculeEnseignant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByCredits(int $credits): array
    {
        return $this->findBy(['credits' => $credits]);
    }
}
