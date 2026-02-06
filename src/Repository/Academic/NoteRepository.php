<?php

declare(strict_types=1);

namespace App\Repository\Academic;

use App\Entity\Academic\Note;
use App\Entity\Academic\TypeNote;
use App\Repository\AbstractRepository;

class NoteRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Note::class;
    }

    public function findByEtudiant(string $matricule): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from($this->getEntityClass(), 'n')
            ->join('n.etudiant', 'e')
            ->where('e.matriculeEtudiant = :matricule')
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getResult();
    }

    public function findByAnneeAcademique(int $idAnneeAcademique): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from($this->getEntityClass(), 'n')
            ->join('n.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :idAnneeAcademique')
            ->setParameter('idAnneeAcademique', $idAnneeAcademique)
            ->getQuery()
            ->getResult();
    }

    public function findBySemestre(int $idSemestre): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from($this->getEntityClass(), 'n')
            ->join('n.semestre', 's')
            ->where('s.idSemestre = :idSemestre')
            ->setParameter('idSemestre', $idSemestre)
            ->getQuery()
            ->getResult();
    }

    public function findByType(TypeNote $typeNote): array
    {
        return $this->findBy(['typeNote' => $typeNote]);
    }
}
