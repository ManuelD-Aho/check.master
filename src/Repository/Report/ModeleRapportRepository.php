<?php

declare(strict_types=1);

namespace App\Repository\Report;

use App\Entity\Report\ModeleRapport;
use App\Repository\AbstractRepository;

class ModeleRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return ModeleRapport::class;
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findByNom(string $nom): ?ModeleRapport
    {
        return $this->findOneBy(['nomModele' => $nom]);
    }
}
