<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\CritereEvaluation;
use App\Repository\AbstractRepository;

class CritereEvaluationRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CritereEvaluation::class;
    }

    public function findByCode(string $code): ?CritereEvaluation
    {
        return $this->findOneBy(['codeCritere' => $code]);
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
