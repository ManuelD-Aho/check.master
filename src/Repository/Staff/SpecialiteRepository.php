<?php

declare(strict_types=1);

namespace App\Repository\Staff;

use App\Entity\Staff\Specialite;
use App\Repository\AbstractRepository;

class SpecialiteRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Specialite::class;
    }

    public function findByCode(string $code): ?Specialite
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeSpecialite' => $code]);
    }

    public function findByLibelle(string $libelle): array
    {
        return $this->findBy(['libelleSpecialite' => $libelle]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
