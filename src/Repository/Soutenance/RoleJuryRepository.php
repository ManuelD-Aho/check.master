<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\RoleJury;
use App\Repository\AbstractRepository;

class RoleJuryRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return RoleJury::class;
    }

    public function findByCode(string $code): ?RoleJury
    {
        return $this->findOneBy(['codeRole' => $code]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findObligatoires(): array
    {
        return $this->findBy(['estObligatoire' => true]);
    }
}
