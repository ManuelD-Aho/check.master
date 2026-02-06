<?php

declare(strict_types=1);

namespace App\Repository\Staff;

use App\Entity\Staff\Grade;
use App\Repository\AbstractRepository;

class GradeRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Grade::class;
    }

    public function findByCode(string $code): ?Grade
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeGrade' => $code]);
    }

    public function findByAbreviation(string $abreviation): array
    {
        return $this->findBy(['abreviation' => $abreviation]);
    }

    public function findByPeutPresiderJury(bool $peutPresiderJury): array
    {
        return $this->findBy(['peutPresiderJury' => $peutPresiderJury]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
