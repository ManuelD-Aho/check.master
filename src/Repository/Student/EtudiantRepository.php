<?php

declare(strict_types=1);

namespace App\Repository\Student;

use App\Entity\Student\Etudiant;
use App\Repository\AbstractRepository;

class EtudiantRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Etudiant::class;
    }

    public function findByMatricule(string $matricule): ?Etudiant
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['matriculeEtudiant' => $matricule]);
    }

    public function findByEmail(string $email): ?Etudiant
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['emailEtudiant' => $email]);
    }

    public function findByPromotion(string $promotion): array
    {
        return $this->findBy(['promotion' => $promotion]);
    }

    public function findByFiliere(int $idFiliere): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.filiere', 'f')
            ->where('f.idFiliere = :idFiliere')
            ->setParameter('idFiliere', $idFiliere)
            ->getQuery()
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
