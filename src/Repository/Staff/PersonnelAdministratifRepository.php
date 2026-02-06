<?php

declare(strict_types=1);

namespace App\Repository\Staff;

use App\Entity\Staff\PersonnelAdministratif;
use App\Repository\AbstractRepository;

class PersonnelAdministratifRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return PersonnelAdministratif::class;
    }

    public function findByMatricule(string $matricule): ?PersonnelAdministratif
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['matriculePersonnel' => $matricule]);
    }

    public function findByEmail(string $email): ?PersonnelAdministratif
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['emailPersonnel' => $email]);
    }

    public function findByPoste(string $poste): array
    {
        return $this->findBy(['poste' => $poste]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
