<?php

declare(strict_types=1);

namespace App\Repository\Staff;

use App\Entity\Staff\Fonction;
use App\Entity\Staff\TypeFonction;
use App\Repository\AbstractRepository;

class FonctionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Fonction::class;
    }

    public function findByCode(string $code): ?Fonction
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeFonction' => $code]);
    }

    public function findByType(TypeFonction $typeFonction): array
    {
        return $this->findBy(['typeFonction' => $typeFonction]);
    }

    public function findByLibelle(string $libelle): array
    {
        return $this->findBy(['libelleFonction' => $libelle]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
