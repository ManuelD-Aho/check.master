<?php

declare(strict_types=1);

namespace App\Repository\Staff;

use App\Entity\Staff\Enseignant;
use App\Entity\Staff\TypeEnseignant;
use App\Repository\AbstractRepository;

class EnseignantRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Enseignant::class;
    }

    public function findByMatricule(string $matricule): ?Enseignant
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['matriculeEnseignant' => $matricule]);
    }

    public function findByEmail(string $email): ?Enseignant
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['emailEnseignant' => $email]);
    }

    public function findBySpecialite(int $idSpecialite): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.specialite', 's')
            ->where('s.idSpecialite = :idSpecialite')
            ->setParameter('idSpecialite', $idSpecialite)
            ->getQuery()
            ->getResult();
    }

    public function findByType(TypeEnseignant $typeEnseignant): array
    {
        return $this->findBy(['typeEnseignant' => $typeEnseignant]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
