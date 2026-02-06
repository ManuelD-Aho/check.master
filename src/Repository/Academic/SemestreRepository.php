<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\Semestre;
use App\Repository\AbstractRepository;

class SemestreRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Semestre::class;
    }

    public function findByCode(string $code): ?Semestre
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeSemestre' => $code]);
    }

    public function findByNiveauEtude(int $idNiveauEtude): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from($this->getEntityClass(), 's')
            ->join('s.niveauEtude', 'n')
            ->where('n.idNiveauEtude = :idNiveauEtude')
            ->setParameter('idNiveauEtude', $idNiveauEtude)
            ->getQuery()
            ->getResult();
    }

    public function findByOrdre(int $ordre): array
    {
        return $this->findBy(['ordre' => $ordre]);
    }
}
