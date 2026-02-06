<?php

declare(strict_types=1);

namespace App\Repository\Student;

use App\Entity\Student\Echeance;
use App\Entity\Student\StatutEcheance;
use App\Repository\AbstractRepository;
use DateTimeInterface;

class EcheanceRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Echeance::class;
    }

    public function findByInscription(int $idInscription): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.inscription', 'i')
            ->where('i.idInscription = :idInscription')
            ->setParameter('idInscription', $idInscription)
            ->getQuery()
            ->getResult();
    }

    public function findByStatut(StatutEcheance $statutEcheance): array
    {
        return $this->findBy(['statutEcheance' => $statutEcheance]);
    }

    public function findByNumero(int $numeroEcheance): array
    {
        return $this->findBy(['numeroEcheance' => $numeroEcheance]);
    }

    public function findByDateEcheance(DateTimeInterface $dateEcheance): array
    {
        return $this->findBy(['dateEcheance' => $dateEcheance]);
    }

    public function findByInscriptionAndNumero(int $idInscription, int $numeroEcheance): ?Echeance
    {
        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityClass(), 'e')
            ->join('e.inscription', 'i')
            ->where('i.idInscription = :idInscription')
            ->andWhere('e.numeroEcheance = :numeroEcheance')
            ->setParameter('idInscription', $idInscription)
            ->setParameter('numeroEcheance', $numeroEcheance)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
