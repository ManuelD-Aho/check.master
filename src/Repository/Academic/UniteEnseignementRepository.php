<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\UniteEnseignement;
use App\Repository\AbstractRepository;

class UniteEnseignementRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return UniteEnseignement::class;
    }

    public function findByCode(string $code): array
    {
        return $this->findBy(['codeUe' => $code]);
    }

    public function findByCodeAndAnnee(string $code, int $idAnneeAcademique): ?UniteEnseignement
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->join('u.anneeAcademique', 'a')
            ->where('u.codeUe = :code')
            ->andWhere('a.idAnneeAcademique = :idAnneeAcademique')
            ->setParameter('code', $code)
            ->setParameter('idAnneeAcademique', $idAnneeAcademique)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByNiveauEtude(int $idNiveauEtude): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->join('u.niveauEtude', 'n')
            ->where('n.idNiveauEtude = :idNiveauEtude')
            ->setParameter('idNiveauEtude', $idNiveauEtude)
            ->getQuery()
            ->getResult();
    }

    public function findBySemestre(int $idSemestre): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->join('u.semestre', 's')
            ->where('s.idSemestre = :idSemestre')
            ->setParameter('idSemestre', $idSemestre)
            ->getQuery()
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->findBy(['actif' => true]);
    }
}
