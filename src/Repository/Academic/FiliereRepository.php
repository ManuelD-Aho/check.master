<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\Filiere;
use App\Repository\AbstractRepository;

class FiliereRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Filiere::class;
    }

    public function findByCode(string $code): ?Filiere
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeFiliere' => $code]);
    }

    public function findByLibelle(string $libelle): array
    {
        return $this->findBy(['libelleFiliere' => $libelle]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
