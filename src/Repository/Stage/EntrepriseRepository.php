<?php

declare(strict_types=1);

namespace App\Repository\Stage;

use App\Entity\Stage\Entreprise;
use App\Repository\AbstractRepository;

class EntrepriseRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Entreprise::class;
    }

    public function findByRaisonSociale(string $name): array
    {
        return $this->findBy(['raisonSociale' => $name]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findBySecteur(string $secteur): array
    {
        return $this->findBy(['secteurActivite' => $secteur]);
    }
}
