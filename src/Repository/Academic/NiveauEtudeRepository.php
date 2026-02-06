<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\NiveauEtude;
use App\Repository\AbstractRepository;

class NiveauEtudeRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return NiveauEtude::class;
    }

    public function findByCode(string $code): ?NiveauEtude
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeNiveau' => $code]);
    }

    public function findByLibelle(string $libelle): array
    {
        return $this->findBy(['libelleNiveau' => $libelle]);
    }

    public function findByOrdreProgression(int $ordreProgression): array
    {
        return $this->findBy(['ordreProgression' => $ordreProgression]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
