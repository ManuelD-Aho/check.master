<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\AnneeAcademique;
use App\Repository\AbstractRepository;

class AnneeAcademiqueRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AnneeAcademique::class;
    }

    public function findByLibelle(string $libelle): ?AnneeAcademique
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['libelleAnnee' => $libelle]);
    }

    public function findActive(): array
    {
        return $this->findBy(['estActive' => true]);
    }
}
