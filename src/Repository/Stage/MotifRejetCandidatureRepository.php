<?php

declare(strict_types=1);

namespace App\Repository\Stage;

use App\Entity\Stage\MotifRejetCandidature;
use App\Repository\AbstractRepository;

class MotifRejetCandidatureRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return MotifRejetCandidature::class;
    }

    public function findByCode(string $code): ?MotifRejetCandidature
    {
        return $this->findOneBy(['codeMotif' => $code]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
