<?php

declare(strict_types=1);

namespace App\Repository\Student;

use App\Entity\Student\MethodePaiement;
use App\Entity\Student\TypeVersement;
use App\Entity\Student\Versement;
use App\Repository\AbstractRepository;

class VersementRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Versement::class;
    }

    public function findByInscription(int $idInscription): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from($this->getEntityClass(), 'v')
            ->join('v.inscription', 'i')
            ->where('i.idInscription = :idInscription')
            ->setParameter('idInscription', $idInscription)
            ->getQuery()
            ->getResult();
    }

    public function findByType(TypeVersement $typeVersement): array
    {
        return $this->findBy(['typeVersement' => $typeVersement]);
    }

    public function findByMethode(MethodePaiement $methodePaiement): array
    {
        return $this->findBy(['methodePaiement' => $methodePaiement]);
    }

    public function findByRecuGenere(bool $recuGenere): array
    {
        return $this->findBy(['recuGenere' => $recuGenere]);
    }

    public function findByReferencePaiement(string $referencePaiement): array
    {
        return $this->findBy(['referencePaiement' => $referencePaiement]);
    }
}
