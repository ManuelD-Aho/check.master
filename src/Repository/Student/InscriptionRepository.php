<?php

declare(strict_types=1);

namespace App\Repository\Student;

use App\Entity\Student\Inscription;
use App\Entity\Student\StatutInscription;
use App\Repository\AbstractRepository;

class InscriptionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Inscription::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from($this->getEntityClass(), 'i')
            ->join('i.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnneeAcademique(int $idAnneeAcademique): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from($this->getEntityClass(), 'i')
            ->join('i.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :idAnneeAcademique')
            ->setParameter('idAnneeAcademique', $idAnneeAcademique)
            ->getQuery()
            ->getResult();
    }

    public function findByStatut(StatutInscription $statutInscription): array
    {
        return $this->findBy(['statutInscription' => $statutInscription]);
    }

    public function findByEtudiantAndAnnee(string $matricule, int $idAnneeAcademique): ?Inscription
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from($this->getEntityClass(), 'i')
            ->join('i.etudiant', 'e')
            ->join('i.anneeAcademique', 'a')
            ->where('e.matriculeEtudiant = :matricule')
            ->andWhere('a.idAnneeAcademique = :idAnneeAcademique')
            ->setParameter('matricule', $matricule)
            ->setParameter('idAnneeAcademique', $idAnneeAcademique)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
